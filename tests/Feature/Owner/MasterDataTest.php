<?php

namespace Tests\Feature\Owner;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductGroup;
use App\Models\ProductMaterial;
use App\Models\RawMaterial;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\SupplierMaterial;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class MasterDataTest extends TestCase
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

        Schema::create('categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 100)->unique();
            $table->timestamps();
        });

        Schema::create('product_groups', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 150);
            $table->foreignId('category_id')->nullable()->constrained('categories')->restrictOnDelete();
            $table->string('production_method', 3);
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('length_cm', 8, 2)->nullable();
            $table->decimal('width_cm', 8, 2)->nullable();
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('group_id')->constrained('product_groups')->restrictOnDelete();
            $table->string('sku', 50)->unique();
            $table->string('variant_name', 100)->nullable();
            $table->string('image_path')->nullable();
            $table->unsignedInteger('current_stock')->default(0);
            $table->unsignedInteger('min_stock')->nullable();
            $table->unsignedInteger('target_stock')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('raw_materials', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 150);
            $table->string('unit', 20);
            $table->string('material_type', 10)->default('NON_SHEET');
            $table->decimal('sheet_length_cm', 8, 2)->nullable();
            $table->decimal('sheet_width_cm', 8, 2)->nullable();
            $table->decimal('current_stock', 12, 3)->default(0);
            $table->decimal('safety_stock', 12, 3)->default(0);
            $table->decimal('target_stock', 12, 3)->default(0);
            $table->decimal('avg_daily_usage', 12, 3)->default(0);
            $table->decimal('reorder_point', 12, 3)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('product_materials', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('group_id')->constrained('product_groups')->cascadeOnDelete();
            $table->foreignId('raw_material_id')->constrained('raw_materials')->restrictOnDelete();
            $table->string('usage_method', 10)->default('AREA');
            $table->decimal('piece_length_cm', 8, 2)->nullable();
            $table->decimal('piece_width_cm', 8, 2)->nullable();
            $table->unsignedSmallInteger('piece_count')->default(1);
            $table->decimal('waste_percent', 5, 2)->default(0);
            $table->decimal('qty_per_unit', 12, 6);
            $table->timestamps();
        });

        Schema::create('suppliers', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 150);
            $table->string('email', 150)->nullable();
            $table->string('phone', 30)->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('supplier_materials', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->foreignId('raw_material_id')->constrained('raw_materials')->restrictOnDelete();
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->unsignedInteger('lead_time_days')->nullable();
            $table->decimal('min_order_qty', 12, 3)->nullable();
            $table->timestamps();
        });

        $this->ownerRole = Role::create(['name' => 'bisnis owner']);
        $this->adminRole = Role::create(['name' => 'admin']);
        $this->productionRole = Role::create(['name' => 'produksi']);
    }

    public function test_guest_cannot_access_master_data_endpoints(): void
    {
        $this->get(route('owner.master-data'))
            ->assertRedirect(route('login'));

        $this->getJson(route('owner.master-data.data'))
            ->assertUnauthorized();
    }

    #[DataProvider('nonOwnerRoleProvider')]
    public function test_non_owner_roles_cannot_access_master_data(string $roleName): void
    {
        $role = $roleName === 'admin' ? $this->adminRole : $this->productionRole;
        $actor = User::factory()->create([
            'role_id' => $role->id,
            'is_active' => true,
        ]);

        $this->actingAs($actor)
            ->get(route('owner.master-data'))
            ->assertForbidden();

        $this->actingAs($actor)
            ->getJson(route('owner.master-data.data'))
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

    public function test_owner_can_view_master_data_and_fetch_grouped_products_and_datasets(): void
    {
        $owner = User::factory()->create([
            'role_id' => $this->ownerRole->id,
            'is_active' => true,
        ]);

        $signCategory = Category::create(['name' => 'SIGN PERINGATAN']);
        $frameCategory = Category::create(['name' => 'KARAKTER']);

        $mtsGroup = ProductGroup::create([
            'code' => 'D6-A',
            'name' => 'Sign Peringatan Hitam (9x30)',
            'category_id' => $signCategory->id,
            'production_method' => 'MTS',
            'price' => 12000,
            'length_cm' => 30,
            'width_cm' => 9,
            'description' => 'MDF cat hitam',
            'is_active' => true,
        ]);

        $mtoGroup = ProductGroup::create([
            'code' => 'FKGU-B-N',
            'name' => 'Frame Kayu Karakter Jepang',
            'category_id' => $frameCategory->id,
            'production_method' => 'MTO',
            'price' => 47000,
            'length_cm' => 30,
            'width_cm' => 10,
            'description' => 'Frame kayu natural',
            'is_active' => true,
        ]);

        $inactiveGroup = ProductGroup::create([
            'code' => 'OLD-01',
            'name' => 'Produk Lama',
            'category_id' => $signCategory->id,
            'production_method' => 'MTS',
            'price' => 15000,
            'is_active' => false,
        ]);

        // Dua varian dalam satu grup MTS (satu aman, satu stok rendah)
        Product::create([
            'group_id' => $mtsGroup->id,
            'sku' => 'D6A-1',
            'variant_name' => 'CCTV',
            'current_stock' => 50,
            'min_stock' => 30,
            'target_stock' => 80,
            'is_active' => true,
        ]);

        Product::create([
            'group_id' => $mtsGroup->id,
            'sku' => 'D6A-5',
            'variant_name' => 'Jagalah Kebersihan',
            'current_stock' => 20,
            'min_stock' => 30,
            'target_stock' => 80,
            'is_active' => true,
        ]);

        // Varian MTO stok 0 tetap Aktif (bukan Stok Rendah)
        Product::create([
            'group_id' => $mtoGroup->id,
            'sku' => 'FKGU-V1',
            'variant_name' => 'V1',
            'current_stock' => 0,
            'min_stock' => null,
            'target_stock' => null,
            'is_active' => true,
        ]);

        // Varian di grup nonaktif menjadi Tidak Aktif
        Product::create([
            'group_id' => $inactiveGroup->id,
            'sku' => 'OLD-V1',
            'variant_name' => null,
            'current_stock' => 10,
            'min_stock' => 5,
            'target_stock' => 20,
            'is_active' => true,
        ]);

        $mdf = RawMaterial::create([
            'code' => 'MDF-6MM',
            'name' => 'mdf 6 mm',
            'unit' => 'lembar',
            'material_type' => 'SHEET',
            'sheet_length_cm' => 244,
            'sheet_width_cm' => 122,
            'current_stock' => 7,
            'safety_stock' => 5,
            'target_stock' => 10,
            'is_active' => true,
        ]);

        $wood = RawMaterial::create([
            'code' => 'WOOD-01',
            'name' => 'kayu pinus',
            'unit' => 'batang',
            'material_type' => 'NON_SHEET',
            'current_stock' => 4,
            'safety_stock' => 5,
            'target_stock' => 10,
            'is_active' => true,
        ]);

        $oldPaint = RawMaterial::create([
            'code' => 'PAINT-OLD',
            'name' => 'cat lama',
            'unit' => 'ml',
            'material_type' => 'NON_SHEET',
            'current_stock' => 100,
            'safety_stock' => 50,
            'target_stock' => 200,
            'is_active' => false,
        ]);

        $bomArea = ProductMaterial::create([
            'group_id' => $mtsGroup->id,
            'raw_material_id' => $mdf->id,
            'usage_method' => 'AREA',
            'piece_length_cm' => 30,
            'piece_width_cm' => 9,
            'piece_count' => 1,
            'waste_percent' => 5.0,
            'qty_per_unit' => 0.009524,
        ]);

        $activeSupplier = Supplier::create([
            'name' => 'CV Kayu Sejahtera',
            'email' => 'sales@kayusejahtera.test',
            'phone' => '081234567801',
            'address' => 'Malang',
            'is_active' => true,
        ]);

        $inactiveSupplier = Supplier::create([
            'name' => 'Supplier Lama',
            'email' => 'lama@supplier.test',
            'phone' => null,
            'address' => 'Sidoarjo',
            'is_active' => false,
        ]);

        SupplierMaterial::create([
            'supplier_id' => $activeSupplier->id,
            'raw_material_id' => $mdf->id,
            'unit_price' => 120000,
            'lead_time_days' => 3,
            'min_order_qty' => 5,
        ]);

        SupplierMaterial::create([
            'supplier_id' => $activeSupplier->id,
            'raw_material_id' => $wood->id,
            'unit_price' => 35000,
            'lead_time_days' => 2,
            'min_order_qty' => 10,
        ]);

        $this->withoutVite()
            ->actingAs($owner)
            ->get(route('owner.master-data'))
            ->assertOk()
            ->assertViewIs('owner.master-data.index');

        $response = $this->actingAs($owner)
            ->getJson(route('owner.master-data.data'));

        $response->assertOk()
            ->assertJsonPath('summary.products', 3)
            ->assertJsonPath('summary.product_variants', 4)
            ->assertJsonPath('summary.categories', 2)
            ->assertJsonPath('summary.materials', 3)
            ->assertJsonPath('summary.suppliers', 2);

        $products = collect($response->json('datasets.products'))->keyBy('code');

        $this->assertCount(3, $products);

        // Grup MTS (2 varian digabung ke 1 baris grup dengan sub-varian)
        $this->assertSame('Sign Peringatan Hitam (9x30)', $products['D6-A']['name']);
        $this->assertSame('SIGN PERINGATAN', $products['D6-A']['category']);
        $this->assertSame(70, $products['D6-A']['stock']);
        $this->assertSame(2, $products['D6-A']['variant_count']);
        $this->assertSame(1, $products['D6-A']['low_stock_count']);
        $this->assertSame('Stok Rendah', $products['D6-A']['status']);
        $this->assertCount(2, $products['D6-A']['variants']);
        $this->assertSame('D6A-1', $products['D6-A']['variants'][0]['sku']);
        $this->assertSame('Aktif', $products['D6-A']['variants'][0]['status']);
        $this->assertSame('D6A-5', $products['D6-A']['variants'][1]['sku']);
        $this->assertSame('Stok Rendah', $products['D6-A']['variants'][1]['status']);

        // Grup MTO
        $this->assertSame('Aktif', $products['FKGU-B-N']['status']);
        $this->assertSame(1, $products['FKGU-B-N']['variant_count']);
        $this->assertSame(0, $products['FKGU-B-N']['stock']);

        // Grup nonaktif
        $this->assertSame('Tidak Aktif', $products['OLD-01']['status']);
        $this->assertSame('Standar', $products['OLD-01']['variants'][0]['variant_name']);
        $this->assertSame('Tidak Aktif', $products['OLD-01']['variants'][0]['status']);

        $response
            ->assertJsonFragment([
                'code' => 'CAT-'.str_pad((string) $signCategory->id, 3, '0', STR_PAD_LEFT),
                'name' => 'SIGN PERINGATAN',
                'total' => 2,
            ])
            ->assertJsonFragment([
                'code' => 'MDF-6MM',
                'status' => 'Tersedia',
                'stock' => 7,
                'minimum' => 5,
            ])
            ->assertJsonFragment([
                'code' => 'WOOD-01',
                'status' => 'Stok Rendah',
            ])
            ->assertJsonFragment([
                'code' => 'PAINT-OLD',
                'status' => 'Tidak Aktif',
            ])
            ->assertJsonFragment([
                'code' => 'BOM-'.str_pad((string) $bomArea->id, 3, '0', STR_PAD_LEFT),
                'product' => 'D6-A — Sign Peringatan Hitam (9x30)',
                'material' => 'mdf 6 mm',
                'quantity' => 0.009524,
                'unit' => 'lembar',
            ])
            ->assertJsonFragment([
                'code' => 'SUP-'.str_pad((string) $activeSupplier->id, 3, '0', STR_PAD_LEFT),
                'name' => 'CV Kayu Sejahtera',
                'contact' => '081234567801',
                'address' => 'Malang',
                'material' => 'mdf 6 mm, kayu pinus',
                'status' => 'Aktif',
            ])
            ->assertJsonFragment([
                'code' => 'SUP-'.str_pad((string) $inactiveSupplier->id, 3, '0', STR_PAD_LEFT),
                'contact' => 'lama@supplier.test',
                'material' => '—',
                'status' => 'Tidak Aktif',
            ]);
    }
}
