<?php

namespace Tests\Feature\Owner;

use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class UserManagementTest extends TestCase
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

        Schema::create('platforms', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 100);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('platform_id')->constrained()->restrictOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->string('platform_order_no', 100);
            $table->string('customer_name', 150);
            $table->date('order_date');
            $table->string('status', 30)->default('NEW');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->timestamps();
        });

        $this->ownerRole = Role::create(['name' => 'bisnis owner']);
        $this->adminRole = Role::create(['name' => 'admin']);
        $this->productionRole = Role::create(['name' => 'produksi']);
    }

    public function test_guest_is_redirected_from_user_management_endpoints(): void
    {
        $this->get(route('owner.user-management'))
            ->assertRedirect(route('login'));

        $this->getJson(route('owner.user-management.data'))
            ->assertUnauthorized();

        $this->postJson(route('owner.user-management.store'), [])
            ->assertUnauthorized();
    }

    #[DataProvider('nonOwnerRoleProvider')]
    public function test_non_owner_roles_cannot_access_user_management(string $roleAttribute): void
    {
        $role = $roleAttribute === 'admin' ? $this->adminRole : $this->productionRole;
        $actor = User::factory()->create([
            'role_id' => $role->id,
            'is_active' => true,
        ]);

        $this->actingAs($actor)
            ->get(route('owner.user-management'))
            ->assertForbidden();

        $this->actingAs($actor)
            ->getJson(route('owner.user-management.data'))
            ->assertForbidden();

        $this->actingAs($actor)
            ->postJson(route('owner.user-management.store'), [
                'name' => 'User Baru',
                'email' => 'baru@rebant.com',
                'password' => 'password123',
                'role' => 'Admin',
                'status' => 'Aktif',
            ])
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

    public function test_owner_can_view_user_management_page_and_fetch_database_users(): void
    {
        $owner = User::factory()->create([
            'role_id' => $this->ownerRole->id,
            'name' => 'Alyfa Zahra',
            'email' => 'owner@rebant.com',
            'is_active' => true,
        ]);
        $admin = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'name' => 'Andi Pratama',
            'email' => 'andi@rebant.com',
            'is_active' => true,
        ]);
        $production = User::factory()->create([
            'role_id' => $this->productionRole->id,
            'name' => 'Dimas Saputra',
            'email' => 'dimas@rebant.com',
            'is_active' => false,
        ]);

        $this->withoutVite()
            ->actingAs($owner)
            ->get(route('owner.user-management'))
            ->assertOk()
            ->assertViewIs('owner.user-management.index')
            ->assertViewHas('lastUpdated');

        $response = $this->actingAs($owner)
            ->getJson(route('owner.user-management.data'));

        $response->assertOk()
            ->assertJsonPath('current_user_id', $owner->id)
            ->assertJsonPath('summary.total', 3)
            ->assertJsonPath('summary.owner', 1)
            ->assertJsonPath('summary.admin', 1)
            ->assertJsonPath('summary.production', 1)
            ->assertJsonFragment([
                'id' => $owner->id,
                'code' => 'USR-'.str_pad((string) $owner->id, 3, '0', STR_PAD_LEFT),
                'name' => 'Alyfa Zahra',
                'email' => 'owner@rebant.com',
                'role' => 'Owner',
                'status' => 'Aktif',
            ])
            ->assertJsonFragment([
                'id' => $admin->id,
                'role' => 'Admin',
                'status' => 'Aktif',
            ])
            ->assertJsonFragment([
                'id' => $production->id,
                'role' => 'Production',
                'status' => 'Nonaktif',
            ]);
    }

    public function test_owner_can_create_user_and_user_can_login(): void
    {
        $owner = User::factory()->create([
            'role_id' => $this->ownerRole->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($owner)
            ->postJson(route('owner.user-management.store'), [
                'name' => 'Nadia Putri',
                'email' => 'NADIA@Rebant.com',
                'password' => 'rahasia123',
                'role' => 'Production',
                'status' => 'Aktif',
            ]);

        $response->assertCreated()
            ->assertJsonPath('user.name', 'Nadia Putri')
            ->assertJsonPath('user.email', 'nadia@rebant.com')
            ->assertJsonPath('user.role', 'Production')
            ->assertJsonPath('user.status', 'Aktif')
            ->assertJsonPath('summary.total', 2)
            ->assertJsonPath('summary.production', 1);

        $created = User::where('email', 'nadia@rebant.com')->first();
        $this->assertNotNull($created);
        $this->assertSame($this->productionRole->id, $created->role_id);
        $this->assertTrue($created->is_active);
        $this->assertTrue(Hash::check('rahasia123', $created->password));

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $owner->id,
            'action' => 'create',
            'entity_type' => 'user',
            'entity_id' => $created->id,
        ]);
    }

    public function test_store_validates_required_fields_and_unique_email(): void
    {
        $owner = User::factory()->create([
            'role_id' => $this->ownerRole->id,
            'email' => 'owner@rebant.com',
            'is_active' => true,
        ]);

        $this->actingAs($owner)
            ->postJson(route('owner.user-management.store'), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'password', 'role', 'status']);

        $this->actingAs($owner)
            ->postJson(route('owner.user-management.store'), [
                'name' => 'Duplicate User',
                'email' => 'OWNER@rebant.com',
                'password' => 'short',
                'role' => 'InvalidRole',
                'status' => 'InvalidStatus',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'password', 'role', 'status']);
    }

    public function test_owner_can_update_user_with_optional_password(): void
    {
        $owner = User::factory()->create([
            'role_id' => $this->ownerRole->id,
            'is_active' => true,
        ]);
        $staff = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'name' => 'Andi Lama',
            'email' => 'andi@rebant.com',
            'password' => 'password-awal',
            'is_active' => true,
        ]);

        // Update tanpa mengubah password
        $this->actingAs($owner)
            ->putJson(route('owner.user-management.update', $staff), [
                'name' => 'Andi Baru',
                'email' => 'andi@rebant.com',
                'role' => 'Production',
                'status' => 'Nonaktif',
            ])
            ->assertOk()
            ->assertJsonPath('user.name', 'Andi Baru')
            ->assertJsonPath('user.role', 'Production')
            ->assertJsonPath('user.status', 'Nonaktif')
            ->assertJsonPath('summary.admin', 0)
            ->assertJsonPath('summary.production', 1);

        $staff->refresh();
        $this->assertSame($this->productionRole->id, $staff->role_id);
        $this->assertFalse($staff->is_active);
        $this->assertTrue(Hash::check('password-awal', $staff->password));

        // Update dengan password baru
        $this->actingAs($owner)
            ->putJson(route('owner.user-management.update', $staff), [
                'name' => 'Andi Baru',
                'email' => 'andi@rebant.com',
                'password' => 'password-baru-99',
                'role' => 'Admin',
                'status' => 'Aktif',
            ])
            ->assertOk()
            ->assertJsonPath('user.role', 'Admin')
            ->assertJsonPath('user.status', 'Aktif');

        $staff->refresh();
        $this->assertTrue(Hash::check('password-baru-99', $staff->password));

        $this->assertSame(2, ActivityLog::where('entity_type', 'user')->where('action', 'update')->count());
    }

    public function test_owner_cannot_deactivate_demote_or_delete_self(): void
    {
        $owner = User::factory()->create([
            'role_id' => $this->ownerRole->id,
            'is_active' => true,
        ]);
        // Tambahkan owner kedua agar guard yang terpicu benar-benar guard akun sendiri
        User::factory()->create([
            'role_id' => $this->ownerRole->id,
            'is_active' => true,
        ]);

        $this->actingAs($owner)
            ->putJson(route('owner.user-management.update', $owner), [
                'name' => $owner->name,
                'email' => $owner->email,
                'role' => 'Owner',
                'status' => 'Nonaktif',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['status']);

        $this->actingAs($owner)
            ->putJson(route('owner.user-management.update', $owner), [
                'name' => $owner->name,
                'email' => $owner->email,
                'role' => 'Admin',
                'status' => 'Aktif',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['role']);

        $this->actingAs($owner)
            ->deleteJson(route('owner.user-management.destroy', $owner))
            ->assertUnprocessable()
            ->assertJsonPath('message', 'Akun yang sedang Anda gunakan tidak dapat dihapus.');
    }

    public function test_cannot_deactivate_demote_or_delete_last_active_owner(): void
    {
        $actingOwner = User::factory()->create([
            'role_id' => $this->ownerRole->id,
            'is_active' => true,
        ]);
        $secondOwner = User::factory()->create([
            'role_id' => $this->ownerRole->id,
            'is_active' => true,
        ]);

        // Saat ada 2 owner aktif, menonaktifkan secondOwner harus berhasil
        $this->actingAs($actingOwner)
            ->putJson(route('owner.user-management.update', $secondOwner), [
                'name' => $secondOwner->name,
                'email' => $secondOwner->email,
                'role' => 'Owner',
                'status' => 'Nonaktif',
            ])
            ->assertOk();

        // Sekarang tinggal actingOwner yang aktif; login sebagai secondOwner (atau test lewat request langsung)
        // untuk memverifikasi proteksi last active owner dari sisi non-self
        $this->actingAs($secondOwner)
            ->putJson(route('owner.user-management.update', $actingOwner), [
                'name' => $actingOwner->name,
                'email' => $actingOwner->email,
                'role' => 'Owner',
                'status' => 'Nonaktif',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['status']);

        $this->actingAs($secondOwner)
            ->putJson(route('owner.user-management.update', $actingOwner), [
                'name' => $actingOwner->name,
                'email' => $actingOwner->email,
                'role' => 'Admin',
                'status' => 'Aktif',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['role']);

        $this->actingAs($secondOwner)
            ->deleteJson(route('owner.user-management.destroy', $actingOwner))
            ->assertUnprocessable()
            ->assertJsonPath('message', 'Akun Business Owner aktif terakhir tidak dapat dihapus.');
    }

    public function test_owner_can_delete_unused_user_but_cannot_delete_user_with_orders(): void
    {
        $owner = User::factory()->create([
            'role_id' => $this->ownerRole->id,
            'is_active' => true,
        ]);
        $unusedAdmin = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'is_active' => true,
        ]);
        $adminWithOrder = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'is_active' => true,
        ]);

        $platformId = DB::table('platforms')->insertGetId([
            'name' => 'Shopee',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('orders')->insert([
            'platform_id' => $platformId,
            'created_by' => $adminWithOrder->id,
            'platform_order_no' => 'SHP-001',
            'customer_name' => 'Budi',
            'order_date' => '2026-11-17',
            'status' => 'NEW',
            'total_amount' => 50000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // User dengan histori transaksi tidak bisa dihapus (FK restrict)
        $this->actingAs($owner)
            ->deleteJson(route('owner.user-management.destroy', $adminWithOrder))
            ->assertUnprocessable();

        $this->assertDatabaseHas('users', ['id' => $adminWithOrder->id]);

        // User tanpa histori bisa dihapus
        $this->actingAs($owner)
            ->deleteJson(route('owner.user-management.destroy', $unusedAdmin))
            ->assertOk()
            ->assertJsonPath('summary.total', 2);

        $this->assertDatabaseMissing('users', ['id' => $unusedAdmin->id]);
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $owner->id,
            'action' => 'delete',
            'entity_type' => 'user',
            'entity_id' => $unusedAdmin->id,
        ]);
    }

    public function test_created_active_user_can_login_while_inactive_user_cannot(): void
    {
        $owner = User::factory()->create([
            'role_id' => $this->ownerRole->id,
            'is_active' => true,
        ]);

        $this->actingAs($owner)
            ->postJson(route('owner.user-management.store'), [
                'name' => 'Sinta Aktif',
                'email' => 'sinta@rebant.com',
                'password' => 'password123',
                'role' => 'Admin',
                'status' => 'Aktif',
            ])
            ->assertCreated();

        $this->actingAs($owner)
            ->postJson(route('owner.user-management.store'), [
                'name' => 'Bima Nonaktif',
                'email' => 'bima@rebant.com',
                'password' => 'password123',
                'role' => 'Production',
                'status' => 'Nonaktif',
            ])
            ->assertCreated();

        auth()->logout();

        $this->post(route('login.process'), [
            'email' => 'bima@rebant.com',
            'password' => 'password123',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();

        $this->post(route('login.process'), [
            'email' => 'sinta@rebant.com',
            'password' => 'password123',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
    }

    public function test_update_prevents_taking_another_users_email(): void
    {
        $owner = User::factory()->create([
            'role_id' => $this->ownerRole->id,
            'email' => 'owner@rebant.com',
            'is_active' => true,
        ]);
        $admin = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'email' => 'admin@rebant.com',
            'is_active' => true,
        ]);

        $this->actingAs($owner)
            ->putJson(route('owner.user-management.update', $admin), [
                'name' => 'Admin',
                'email' => 'OWNER@rebant.com',
                'role' => 'Admin',
                'status' => 'Aktif',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }
}
