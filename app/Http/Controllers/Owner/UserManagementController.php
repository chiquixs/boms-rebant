<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    private const UI_OWNER = 'Owner';

    private const UI_ADMIN = 'Admin';

    private const UI_PRODUCTION = 'Production';

    private const STATUS_ACTIVE = 'Aktif';

    private const STATUS_INACTIVE = 'Nonaktif';

    /**
     * Urutan kandidat nama role di tabel roles untuk tiap role UI.
     * Prioritas pertama mengikuti middleware autentikasi aktif di routes/web.php.
     *
     * @var array<string, list<string>>
     */
    private const ROLE_TO_DB_NAMES = [
        self::UI_OWNER => ['bisnis owner', 'owner'],
        self::UI_ADMIN => ['admin'],
        self::UI_PRODUCTION => ['produksi', 'production'],
    ];

    /**
     * Tampilkan halaman User & Access Management.
     */
    public function index(): View
    {
        return view('owner.user-management.index', [
            'lastUpdated' => $this->lastUpdatedLabel(),
        ]);
    }

    /**
     * Ambil seluruh daftar pengguna dan ringkasan statistik untuk halaman Owner.
     */
    public function data(Request $request): JsonResponse
    {
        $users = User::with('role')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get()
            ->map(fn (User $user): array => $this->formatUser($user))
            ->values();

        return response()->json([
            'users' => $users,
            'summary' => $this->buildSummary($users->all()),
            'last_updated' => $this->lastUpdatedLabel(),
            'current_user_id' => $request->user()?->id,
        ]);
    }

    /**
     * Simpan pengguna baru ke database.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $this->validateUser($request);
        $role = $this->resolveRole($validated['role']);
        $isActive = $validated['status'] === self::STATUS_ACTIVE;

        $created = DB::transaction(function () use ($validated, $role, $isActive): User {
            $user = User::create([
                'role_id' => $role->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'is_active' => $isActive,
            ]);

            $user->setRelation('role', $role);

            ActivityLog::record('create', 'user', $user->id, 'Pengguna baru ditambahkan.', [
                'title' => $user->name,
                'subtitle' => $validated['role'].' · '.$user->email,
            ]);

            return $user;
        });

        return response()->json([
            'message' => 'Pengguna baru berhasil ditambahkan.',
            'user' => $this->formatUser($created),
            'summary' => $this->summaryFromDatabase(),
            'last_updated' => $this->lastUpdatedLabel(),
        ], 201);
    }

    /**
     * Perbarui data, role, status, dan opsional password pengguna.
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $this->validateUser($request, $user);
        $targetRole = $this->resolveRole($validated['role']);
        $targetIsActive = $validated['status'] === self::STATUS_ACTIVE;
        $actingUserId = $request->user()?->id;

        if ($user->id === $actingUserId) {
            if (! $targetIsActive) {
                throw ValidationException::withMessages([
                    'status' => 'Akun yang sedang Anda gunakan tidak dapat dinonaktifkan.',
                ]);
            }

            if ($validated['role'] !== self::UI_OWNER) {
                throw ValidationException::withMessages([
                    'role' => 'Role akun yang sedang Anda gunakan tidak dapat diubah.',
                ]);
            }
        }

        $updated = DB::transaction(function () use ($user, $validated, $targetRole, $targetIsActive): User {
            $lockedUser = User::with('role')->lockForUpdate()->findOrFail($user->id);

            $losingOwnerPrivilege = $this->isOwnerRole($lockedUser->role?->name)
                && $lockedUser->is_active
                && (! $targetIsActive || $validated['role'] !== self::UI_OWNER);

            if ($losingOwnerPrivilege && $this->isOnlyActiveOwner($lockedUser->id)) {
                $errorField = ! $targetIsActive ? 'status' : 'role';
                $errorMessage = ! $targetIsActive
                    ? 'Akun Business Owner aktif terakhir tidak dapat dinonaktifkan.'
                    : 'Role akun Business Owner aktif terakhir tidak dapat diubah.';

                throw ValidationException::withMessages([
                    $errorField => $errorMessage,
                ]);
            }

            $attributes = [
                'role_id' => $targetRole->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'is_active' => $targetIsActive,
            ];

            if (! empty($validated['password'])) {
                $attributes['password'] = $validated['password'];
            }

            $lockedUser->update($attributes);
            $lockedUser->setRelation('role', $targetRole);

            ActivityLog::record('update', 'user', $lockedUser->id, 'Data pengguna diperbarui.', [
                'title' => $lockedUser->name,
                'subtitle' => $validated['role'].' · '.$validated['status'],
            ]);

            return $lockedUser;
        });

        return response()->json([
            'message' => 'Data pengguna berhasil diperbarui.',
            'user' => $this->formatUser($updated),
            'summary' => $this->summaryFromDatabase(),
            'last_updated' => $this->lastUpdatedLabel(),
        ]);
    }

    /**
     * Hapus akun pengguna bila tidak melanggar integritas relasi maupun aturan Owner terakhir.
     */
    public function destroy(Request $request, User $user): JsonResponse
    {
        if ($user->id === $request->user()?->id) {
            return response()->json([
                'message' => 'Akun yang sedang Anda gunakan tidak dapat dihapus.',
            ], 422);
        }

        $userId = $user->id;
        $userName = $user->name;
        $userEmail = $user->email;

        try {
            DB::transaction(function () use ($userId): void {
                $lockedUser = User::with('role')->lockForUpdate()->findOrFail($userId);

                if (
                    $lockedUser->is_active
                    && $this->isOwnerRole($lockedUser->role?->name)
                    && $this->isOnlyActiveOwner($lockedUser->id)
                ) {
                    throw ValidationException::withMessages([
                        'user' => 'Akun Business Owner aktif terakhir tidak dapat dihapus.',
                    ]);
                }

                $deletedName = $lockedUser->name;
                $deletedEmail = $lockedUser->email;

                $lockedUser->delete();

                ActivityLog::record('delete', 'user', $userId, 'Pengguna dihapus.', [
                    'title' => $deletedName,
                    'subtitle' => $deletedEmail,
                ]);
            });
        } catch (ValidationException $e) {
            $firstMessage = collect($e->errors())->flatten()->first()
                ?? 'Akun Business Owner aktif terakhir tidak dapat dihapus.';

            return response()->json([
                'message' => $firstMessage,
            ], 422);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Pengguna tidak dapat dihapus karena masih memiliki riwayat transaksi atau produksi ('.$userName.' · '.$userEmail.'). Ubah status akun menjadi Nonaktif saja.',
            ], 422);
        }

        return response()->json([
            'message' => 'Pengguna berhasil dihapus.',
            'summary' => $this->summaryFromDatabase(),
            'last_updated' => $this->lastUpdatedLabel(),
        ]);
    }

    /**
     * Validasi dan normalisasi payload create/update pengguna.
     *
     * @return array<string, mixed>
     */
    private function validateUser(Request $request, ?User $user = null): array
    {
        $request->merge([
            'name' => trim((string) $request->input('name')),
            'email' => mb_strtolower(trim((string) $request->input('email'))),
        ]);

        $isUpdate = $user !== null;

        return $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                $isUpdate ? Rule::unique('users', 'email')->ignore($user->id) : Rule::unique('users', 'email'),
            ],
            'password' => $isUpdate
                ? ['nullable', 'string', 'min:8', 'max:255']
                : ['required', 'string', 'min:8', 'max:255'],
            'role' => ['required', Rule::in([self::UI_OWNER, self::UI_ADMIN, self::UI_PRODUCTION])],
            'status' => ['required', Rule::in([self::STATUS_ACTIVE, self::STATUS_INACTIVE])],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.min' => 'Nama minimal 3 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'role.required' => 'Silakan pilih role pengguna.',
            'role.in' => 'Role pengguna tidak valid.',
            'status.required' => 'Silakan pilih status akun.',
            'status.in' => 'Status akun tidak valid.',
        ]);
    }

    /**
     * Temukan entitas Role di database yang sesuai dengan pilihan role UI.
     */
    private function resolveRole(string $uiRole): Role
    {
        $candidates = self::ROLE_TO_DB_NAMES[$uiRole] ?? [];
        $roles = Role::whereIn('name', $candidates)->get()->keyBy('name');

        foreach ($candidates as $candidate) {
            if ($roles->has($candidate)) {
                return $roles->get($candidate);
            }
        }

        throw ValidationException::withMessages([
            'role' => 'Role pengguna belum tersedia di database.',
        ]);
    }

    /**
     * Periksa apakah nama role database termasuk role Business Owner.
     */
    private function isOwnerRole(?string $dbRoleName): bool
    {
        if ($dbRoleName === null) {
            return false;
        }

        return in_array(mb_strtolower($dbRoleName), self::ROLE_TO_DB_NAMES[self::UI_OWNER], true);
    }

    /**
     * Pastikan tidak ada Business Owner aktif lain selain user yang diberikan.
     */
    private function isOnlyActiveOwner(int $userId): bool
    {
        return ! User::where('is_active', true)
            ->where('id', '!=', $userId)
            ->whereHas('role', function ($query): void {
                $query->whereIn('name', self::ROLE_TO_DB_NAMES[self::UI_OWNER]);
            })
            ->lockForUpdate()
            ->exists();
    }

    /**
     * Petakan nama role database ke key role UI.
     */
    private function toUiRole(?string $dbRoleName): string
    {
        $normalized = mb_strtolower((string) $dbRoleName);

        foreach (self::ROLE_TO_DB_NAMES as $uiRole => $dbNames) {
            if (in_array($normalized, $dbNames, true)) {
                return $uiRole;
            }
        }

        return self::UI_PRODUCTION;
    }

    /**
     * Format satu record User menjadi struktur JSON untuk frontend.
     *
     * @return array<string, mixed>
     */
    private function formatUser(User $user): array
    {
        return [
            'id' => $user->id,
            'code' => 'USR-'.str_pad((string) $user->id, 3, '0', STR_PAD_LEFT),
            'name' => $user->name,
            'email' => $user->email,
            'role' => $this->toUiRole($user->role?->name),
            'joined' => $user->created_at?->toDateString() ?? now()->toDateString(),
            'status' => $user->is_active ? self::STATUS_ACTIVE : self::STATUS_INACTIVE,
        ];
    }

    /**
     * Hitung ringkasan jumlah pengguna per role dari seluruh data user.
     *
     * @param  array<int, array<string, mixed>>  $formattedUsers
     * @return array<string, int>
     */
    private function buildSummary(array $formattedUsers): array
    {
        $summary = [
            'total' => count($formattedUsers),
            'owner' => 0,
            'admin' => 0,
            'production' => 0,
        ];

        foreach ($formattedUsers as $user) {
            if ($user['role'] === self::UI_OWNER) {
                $summary['owner']++;
            } elseif ($user['role'] === self::UI_ADMIN) {
                $summary['admin']++;
            } elseif ($user['role'] === self::UI_PRODUCTION) {
                $summary['production']++;
            }
        }

        return $summary;
    }

    /**
     * Ambil ringkasan jumlah pengguna langsung dari database.
     *
     * @return array<string, int>
     */
    private function summaryFromDatabase(): array
    {
        $counts = User::query()
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->selectRaw('LOWER(roles.name) as role_name, COUNT(*) as aggregate')
            ->groupByRaw('LOWER(roles.name)')
            ->get();

        $summary = [
            'total' => 0,
            'owner' => 0,
            'admin' => 0,
            'production' => 0,
        ];

        foreach ($counts as $row) {
            $count = (int) $row->aggregate;
            $summary['total'] += $count;

            $uiRole = $this->toUiRole($row->role_name);

            if ($uiRole === self::UI_OWNER) {
                $summary['owner'] += $count;
            } elseif ($uiRole === self::UI_ADMIN) {
                $summary['admin'] += $count;
            } elseif ($uiRole === self::UI_PRODUCTION) {
                $summary['production'] += $count;
            }
        }

        return $summary;
    }

    /**
     * Format tanggal pembaruan data pengguna terbaru dalam bahasa Indonesia.
     */
    private function lastUpdatedLabel(): string
    {
        $latest = User::max('updated_at');
        $date = $latest ? Carbon::parse($latest) : now();

        return $date->locale('id')->translatedFormat('d F Y');
    }
}
