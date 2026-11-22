<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    /**
     * Tampilkan halaman Log Aktivitas untuk Business Owner.
     */
    public function index(): View
    {
        return view('owner.activity-log.index', [
            'modules' => ActivityLog::MODULES,
        ]);
    }

    /**
     * Ambil daftar log aktivitas dan ringkasan statistik dari database sesuai filter.
     */
    public function data(Request $request): JsonResponse
    {
        $allowedCategories = array_merge(['all'], array_keys(ActivityLog::MODULES));

        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', Rule::in($allowedCategories)],
            'start' => ['nullable', 'date'],
            'end' => ['nullable', 'date', 'after_or_equal:start'],
        ], [
            'end.after_or_equal' => 'Tanggal akhir harus setelah atau sama dengan tanggal awal.',
        ]);

        $search = trim((string) ($validated['search'] ?? ''));
        $category = $validated['category'] ?? 'all';
        $start = $validated['start'] ?? null;
        $end = $validated['end'] ?? null;

        $query = ActivityLog::with(['user.role'])
            ->when($category !== '' && $category !== 'all', function (Builder $builder) use ($category): void {
                $builder->where('entity_type', $category);
            })
            ->when($start !== null, function (Builder $builder) use ($start): void {
                $builder->where('created_at', '>=', Carbon::parse($start)->startOfDay());
            })
            ->when($end !== null, function (Builder $builder) use ($end): void {
                $builder->where('created_at', '<=', Carbon::parse($end)->endOfDay());
            })
            ->when($search !== '', function (Builder $builder) use ($search): void {
                $this->applySearchFilter($builder, $search);
            });

        $logs = $query
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        $activities = $logs
            ->map(fn (ActivityLog $log): array => $this->formatLog($log))
            ->values();

        return response()->json([
            'summary' => [
                'total' => $logs->count(),
                'order' => $logs->where('entity_type', 'order')->count(),
                'production' => $logs->where('entity_type', 'production')->count(),
                'inventory' => $logs->filter(
                    fn (ActivityLog $log): bool => in_array($log->entity_type, ['inventory', 'material'], true)
                )->count(),
            ],
            'activities' => $activities,
        ]);
    }

    /**
     * Terapkan filter pencarian teks/ID aktivitas yang kompatibel dengan PostgreSQL dan SQLite.
     */
    private function applySearchFilter(Builder $builder, string $search): void
    {
        $isPgsql = DB::connection()->getDriverName() === 'pgsql';
        $operator = $isPgsql ? 'ilike' : 'like';
        $pattern = '%'.$search.'%';
        $lowerPattern = '%'.mb_strtolower($search).'%';

        $matchedNumericId = null;
        if (preg_match('/^act-(\d+)$/i', $search, $matches)) {
            $matchedNumericId = (int) $matches[1];
        } elseif (ctype_digit($search)) {
            $matchedNumericId = (int) $search;
        }

        $matchingEntityTypes = collect(ActivityLog::MODULES)
            ->filter(fn (string $label): bool => str_contains(mb_strtolower($label), mb_strtolower($search)))
            ->keys()
            ->all();

        $matchingActions = collect(ActivityLog::ACTIONS)
            ->filter(fn (string $label): bool => str_contains(mb_strtolower($label), mb_strtolower($search)))
            ->keys()
            ->all();

        $builder->where(function (Builder $nested) use (
            $isPgsql,
            $operator,
            $pattern,
            $lowerPattern,
            $matchedNumericId,
            $matchingEntityTypes,
            $matchingActions
        ): void {
            $nested->where('description', $operator, $pattern)
                ->orWhere('action', $operator, $pattern)
                ->orWhere('entity_type', $operator, $pattern)
                ->orWhereHas('user', function (Builder $userQuery) use ($operator, $pattern): void {
                    $userQuery->where('name', $operator, $pattern);
                });

            if ($isPgsql) {
                $nested->orWhereRaw('metadata::text ilike ?', [$pattern]);
            } else {
                $nested->orWhereRaw("LOWER(COALESCE(metadata, '')) LIKE ?", [$lowerPattern]);
            }

            if ($matchedNumericId !== null) {
                $nested->orWhere('id', $matchedNumericId);
            }

            if (! empty($matchingEntityTypes)) {
                $nested->orWhereIn('entity_type', $matchingEntityTypes);
            }

            if (! empty($matchingActions)) {
                $nested->orWhereIn('action', $matchingActions);
            }
        });
    }

    /**
     * Format satu record ActivityLog untuk tabel, ringkasan, modal detail, dan ekspor.
     *
     * @return array<string, mixed>
     */
    private function formatLog(ActivityLog $log): array
    {
        $code = 'ACT-'.str_pad((string) $log->id, 3, '0', STR_PAD_LEFT);
        $description = trim((string) ($log->description ?? ''));
        $subtitle = trim((string) $log->detail_subtitle);

        $detailParts = array_filter([$description, $subtitle], fn (string $part): bool => $part !== '');
        $detailText = ! empty($detailParts) ? implode(' · ', $detailParts) : '—';

        return [
            'id' => $code,
            'numeric_id' => $log->id,
            'date' => $log->created_at?->format('Y-m-d') ?? '',
            'time' => $log->created_at?->format('H:i') ?? '00:00',
            'entity_type' => $log->entity_type,
            'category' => $log->module_label,
            'action' => $log->title,
            'user' => $log->user?->name ?? 'Sistem',
            'user_role' => $this->formatRoleLabel($log->user?->role?->name),
            'reference' => $log->detail_title ?: '—',
            'detail' => $detailText,
        ];
    }

    /**
     * Ubah nama role database menjadi label yang mudah dibaca.
     */
    private function formatRoleLabel(?string $roleName): string
    {
        return match (mb_strtolower((string) $roleName)) {
            'bisnis owner', 'owner' => 'Business Owner',
            'admin' => 'Admin',
            'produksi', 'production' => 'Production',
            default => 'Sistem',
        };
    }
}
