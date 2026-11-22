<?php

namespace Tests\Feature\Owner;

use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    private Role $ownerRole;

    private Role $adminRole;

    private Role $productionRole;

    protected function setUp(): void
    {
        parent::setUp();

        DB::statement('PRAGMA foreign_keys = ON');

        Schema::dropAllTables();

        Schema::create('roles', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 30)->unique();
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->restrictOnDelete();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('activity_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 50);
            $table->string('entity_type', 50);
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->text('description')->nullable();
            $table->text('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        $this->ownerRole = Role::create(['name' => 'bisnis owner']);
        $this->adminRole = Role::create(['name' => 'admin']);
        $this->productionRole = Role::create(['name' => 'produksi']);
    }

    public function test_guest_cannot_access_activity_log_endpoints(): void
    {
        $this->get(route('owner.activity-log'))
            ->assertRedirect(route('login'));

        $this->getJson(route('owner.activity-log.data'))
            ->assertUnauthorized();
    }

    #[DataProvider('nonOwnerRoleProvider')]
    public function test_non_owner_roles_cannot_access_activity_log(string $roleName): void
    {
        $role = $roleName === 'admin' ? $this->adminRole : $this->productionRole;
        $actor = User::factory()->create([
            'role_id' => $role->id,
            'is_active' => true,
        ]);

        $this->actingAs($actor)
            ->get(route('owner.activity-log'))
            ->assertForbidden();

        $this->actingAs($actor)
            ->getJson(route('owner.activity-log.data'))
            ->assertForbidden();
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function nonOwnerRoleProvider(): array
    {
        return [
            'admin' => ['admin'],
            'production' => ['produksi'],
        ];
    }

    public function test_owner_can_view_page_and_fetch_formatted_activity_logs(): void
    {
        $owner = User::factory()->create([
            'role_id' => $this->ownerRole->id,
            'name' => 'Alyfa Owner',
            'is_active' => true,
        ]);
        $admin = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'name' => 'Andi Admin',
            'is_active' => true,
        ]);
        $production = User::factory()->create([
            'role_id' => $this->productionRole->id,
            'name' => 'Nadia Produksi',
            'is_active' => true,
        ]);

        $orderLog = ActivityLog::create([
            'user_id' => $admin->id,
            'action' => 'create',
            'entity_type' => 'order',
            'entity_id' => 12,
            'description' => 'Order baru ditambahkan.',
            'metadata' => ['title' => 'SHP-012', 'subtitle' => 'Status New'],
            'created_at' => '2026-11-10 09:15:00',
        ]);

        $productionLog = ActivityLog::create([
            'user_id' => $production->id,
            'action' => 'process',
            'entity_type' => 'production',
            'entity_id' => 8,
            'description' => 'Produksi selesai dijalankan.',
            'metadata' => ['activity' => 'Menyelesaikan Batch Produksi', 'title' => 'PR-008'],
            'created_at' => '2026-11-12 14:30:00',
        ]);

        $materialLog = ActivityLog::create([
            'user_id' => null,
            'action' => 'update',
            'entity_type' => 'material',
            'entity_id' => 3,
            'description' => 'Stok bahan mentah disesuaikan.',
            'metadata' => ['title' => 'MDF-6MM'],
            'created_at' => '2026-11-15 16:00:00',
        ]);

        $this->withoutVite()
            ->actingAs($owner)
            ->get(route('owner.activity-log'))
            ->assertOk()
            ->assertViewIs('owner.activity-log.index')
            ->assertViewHas('modules', ActivityLog::MODULES);

        $response = $this->actingAs($owner)
            ->getJson(route('owner.activity-log.data'));

        $response->assertOk()
            ->assertJsonPath('summary.total', 3)
            ->assertJsonPath('summary.order', 1)
            ->assertJsonPath('summary.production', 1)
            ->assertJsonPath('summary.inventory', 1)
            // Urutan terbaru di paling atas
            ->assertJsonPath('activities.0.id', 'ACT-'.str_pad((string) $materialLog->id, 3, '0', STR_PAD_LEFT))
            ->assertJsonPath('activities.0.user', 'Sistem')
            ->assertJsonPath('activities.0.user_role', 'Sistem')
            ->assertJsonPath('activities.0.category', 'Bahan Mentah')
            ->assertJsonPath('activities.0.reference', 'MDF-6MM')
            ->assertJsonPath('activities.1.id', 'ACT-'.str_pad((string) $productionLog->id, 3, '0', STR_PAD_LEFT))
            ->assertJsonPath('activities.1.action', 'Menyelesaikan Batch Produksi')
            ->assertJsonPath('activities.1.user', 'Nadia Produksi')
            ->assertJsonPath('activities.1.user_role', 'Production')
            ->assertJsonPath('activities.2.id', 'ACT-'.str_pad((string) $orderLog->id, 3, '0', STR_PAD_LEFT))
            ->assertJsonPath('activities.2.category', 'Order')
            ->assertJsonPath('activities.2.action', 'Menambah Order')
            ->assertJsonPath('activities.2.user', 'Andi Admin')
            ->assertJsonPath('activities.2.user_role', 'Admin')
            ->assertJsonPath('activities.2.detail', 'Order baru ditambahkan. · Status New');
    }

    public function test_owner_can_filter_activity_logs_by_category_date_and_search(): void
    {
        $owner = User::factory()->create([
            'role_id' => $this->ownerRole->id,
            'is_active' => true,
        ]);
        $admin = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'name' => 'Citra Admin',
            'is_active' => true,
        ]);

        $firstLog = ActivityLog::create([
            'user_id' => $admin->id,
            'action' => 'create',
            'entity_type' => 'order',
            'entity_id' => 10,
            'description' => 'Order Shopee dicatat.',
            'metadata' => ['title' => 'SHP-999'],
            'created_at' => '2026-11-05 10:00:00',
        ]);

        ActivityLog::create([
            'user_id' => $owner->id,
            'action' => 'create',
            'entity_type' => 'user',
            'entity_id' => 5,
            'description' => 'Pengguna baru ditambahkan.',
            'metadata' => ['title' => 'Staf Baru'],
            'created_at' => '2026-11-20 11:00:00',
        ]);

        // Filter kategori
        $this->actingAs($owner)
            ->getJson(route('owner.activity-log.data', ['category' => 'order']))
            ->assertOk()
            ->assertJsonPath('summary.total', 1)
            ->assertJsonPath('summary.order', 1)
            ->assertJsonPath('activities.0.reference', 'SHP-999');

        // Filter rentang tanggal
        $this->actingAs($owner)
            ->getJson(route('owner.activity-log.data', [
                'start' => '2026-11-18',
                'end' => '2026-11-22',
            ]))
            ->assertOk()
            ->assertJsonPath('summary.total', 1)
            ->assertJsonPath('activities.0.category', 'Pengguna');

        // Pencarian dengan format ID ACT-001
        $actCode = 'ACT-'.str_pad((string) $firstLog->id, 3, '0', STR_PAD_LEFT);
        $this->actingAs($owner)
            ->getJson(route('owner.activity-log.data', ['search' => $actCode]))
            ->assertOk()
            ->assertJsonPath('summary.total', 1)
            ->assertJsonPath('activities.0.id', $actCode);

        // Pencarian berdasarkan nama user
        $this->actingAs($owner)
            ->getJson(route('owner.activity-log.data', ['search' => 'Citra']))
            ->assertOk()
            ->assertJsonPath('summary.total', 1)
            ->assertJsonPath('activities.0.user', 'Citra Admin');
    }

    public function test_invalid_date_range_or_category_returns_validation_error(): void
    {
        $owner = User::factory()->create([
            'role_id' => $this->ownerRole->id,
            'is_active' => true,
        ]);

        $this->actingAs($owner)
            ->getJson(route('owner.activity-log.data', [
                'start' => '2026-11-20',
                'end' => '2026-11-10',
            ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['end']);

        $this->actingAs($owner)
            ->getJson(route('owner.activity-log.data', [
                'category' => 'unknown_module',
            ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['category']);
    }
}
