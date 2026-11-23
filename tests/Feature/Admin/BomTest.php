<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\ProductGroup;
use App\Models\ProductMaterial;
use App\Models\RawMaterial;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BomTest extends TestCase
{
    private User $admin;

    private ProductGroup $group;

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

        $adminRole = Role::create(['name' => 'admin']);
        $this->admin = User::factory()->create([
            'role_id' => $adminRole->id,
            'is_active' => true,
        ]);

        $category = Category::create(['name' => 'Frame Kayu']);
        $this->group = ProductGroup::create([
            'code' => 'FRM-A4',
            'name' => 'Frame Kayu A4',
            'category_id' => $category->id,
            'production_method' => 'MTO',
            'price' => 75000,
            'length_cm' => 30,
            'width_cm' => 21,
            'is_active' => true,
        ]);
    }

    public function test_non_sheet_material_automatically_saves_as_fixed_ignoring_client_usage_method(): void
    {
        $wood = RawMaterial::create([
            'code' => 'WOOD-01',
            'name' => 'Kayu Pinus',
            'unit' => 'batang',
            'material_type' => 'NON_SHEET',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.products.bom.store', $this->group->code), [
                'raw_material_id' => $wood->id,
                'usage_method' => 'area',
                'quantity' => 4,
            ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $bom = ProductMaterial::query()
            ->where('group_id', $this->group->id)
            ->where('raw_material_id', $wood->id)
            ->first();

        $this->assertNotNull($bom);
        $this->assertSame('FIXED', $bom->usage_method);
        $this->assertEquals(4, (float) $bom->qty_per_unit);
        $this->assertEquals(0, (float) $bom->waste_percent);
        $this->assertNull($bom->piece_length_cm);
        $this->assertNull($bom->piece_width_cm);
        $this->assertSame(1, (int) $bom->piece_count);
    }

    public function test_sheet_material_automatically_saves_as_area_ignoring_client_usage_method(): void
    {
        $mdf = RawMaterial::create([
            'code' => 'MDF-6MM',
            'name' => 'Papan MDF 6mm',
            'unit' => 'lembar',
            'material_type' => 'SHEET',
            'sheet_length_cm' => 100,
            'sheet_width_cm' => 200,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.products.bom.store', $this->group->code), [
                'raw_material_id' => $mdf->id,
                'usage_method' => 'fix',
                'piece_length_cm' => 50,
                'piece_width_cm' => 40,
                'piece_count' => 2,
                'waste_percent' => 10,
            ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $bom = ProductMaterial::query()
            ->where('group_id', $this->group->id)
            ->where('raw_material_id', $mdf->id)
            ->first();

        $this->assertNotNull($bom);
        $this->assertSame('AREA', $bom->usage_method);
        $this->assertEqualsWithDelta(0.22, (float) $bom->qty_per_unit, 0.000001);
        $this->assertEquals(50, (float) $bom->piece_length_cm);
        $this->assertEquals(40, (float) $bom->piece_width_cm);
        $this->assertSame(2, (int) $bom->piece_count);
        $this->assertEquals(10, (float) $bom->waste_percent);
    }

    public function test_sheet_material_without_sheet_dimensions_is_rejected(): void
    {
        $incompleteSheet = RawMaterial::create([
            'code' => 'ACR-3MM',
            'name' => 'Akrilik 3mm',
            'unit' => 'lembar',
            'material_type' => 'SHEET',
            'sheet_length_cm' => null,
            'sheet_width_cm' => null,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.products.bom.store', $this->group->code), [
                'raw_material_id' => $incompleteSheet->id,
                'piece_length_cm' => 30,
                'piece_width_cm' => 20,
                'piece_count' => 1,
                'waste_percent' => 5,
            ]);

        $response->assertSessionHasErrors('raw_material_id');
        $this->assertDatabaseCount('product_materials', 0);
    }

    public function test_validation_rules_match_derived_material_method(): void
    {
        $wood = RawMaterial::create([
            'code' => 'WOOD-02',
            'name' => 'Kayu Jati',
            'unit' => 'batang',
            'material_type' => 'NON_SHEET',
            'is_active' => true,
        ]);

        $sheet = RawMaterial::create([
            'code' => 'GLASS-2MM',
            'name' => 'Kaca Bening 2mm',
            'unit' => 'lembar',
            'material_type' => 'SHEET',
            'sheet_length_cm' => 120,
            'sheet_width_cm' => 240,
            'is_active' => true,
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.products.bom.store', $this->group->code), [
                'raw_material_id' => $wood->id,
            ])
            ->assertSessionHasErrors('quantity');

        $this->actingAs($this->admin)
            ->post(route('admin.products.bom.store', $this->group->code), [
                'raw_material_id' => $sheet->id,
                'quantity' => 1,
            ])
            ->assertSessionHasErrors(['piece_length_cm', 'piece_width_cm', 'piece_count', 'waste_percent']);
    }

    public function test_updating_bom_item_switches_usage_method_automatically_from_selected_material(): void
    {
        $wood = RawMaterial::create([
            'code' => 'WOOD-01',
            'name' => 'Kayu Pinus',
            'unit' => 'batang',
            'material_type' => 'NON_SHEET',
            'is_active' => true,
        ]);

        $sheet = RawMaterial::create([
            'code' => 'MDF-6MM',
            'name' => 'MDF 6mm',
            'unit' => 'lembar',
            'material_type' => 'SHEET',
            'sheet_length_cm' => 120,
            'sheet_width_cm' => 240,
            'is_active' => true,
        ]);

        $bom = ProductMaterial::create([
            'group_id' => $this->group->id,
            'raw_material_id' => $wood->id,
            'usage_method' => 'FIXED',
            'qty_per_unit' => 2,
            'waste_percent' => 0,
            'piece_count' => 1,
        ]);

        $this->actingAs($this->admin)
            ->put(route('admin.products.bom.update', [$this->group->code, $bom->id]), [
                'raw_material_id' => $sheet->id,
                'piece_length_cm' => 60,
                'piece_width_cm' => 60,
                'piece_count' => 1,
                'waste_percent' => 20,
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $bom->refresh();
        $this->assertSame($sheet->id, $bom->raw_material_id);
        $this->assertSame('AREA', $bom->usage_method);
        $this->assertEqualsWithDelta(0.15, (float) $bom->qty_per_unit, 0.000001);

        $this->actingAs($this->admin)
            ->put(route('admin.products.bom.update', [$this->group->code, $bom->id]), [
                'raw_material_id' => $wood->id,
                'quantity' => 3,
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $bom->refresh();
        $this->assertSame($wood->id, $bom->raw_material_id);
        $this->assertSame('FIXED', $bom->usage_method);
        $this->assertEquals(3, (float) $bom->qty_per_unit);
        $this->assertNull($bom->piece_length_cm);
        $this->assertNull($bom->piece_width_cm);
        $this->assertSame(1, (int) $bom->piece_count);
    }

    public function test_bom_page_renders_auto_method_preview_without_manual_radios(): void
    {
        RawMaterial::create([
            'code' => 'WOOD-01',
            'name' => 'Kayu Pinus',
            'unit' => 'batang',
            'material_type' => 'NON_SHEET',
            'is_active' => true,
        ]);

        RawMaterial::create([
            'code' => 'MDF-6MM',
            'name' => 'MDF 6mm',
            'unit' => 'lembar',
            'material_type' => 'SHEET',
            'sheet_length_cm' => 122,
            'sheet_width_cm' => 244,
            'is_active' => true,
        ]);

        $this->withoutVite()
            ->actingAs($this->admin)
            ->get(route('admin.products.bom', $this->group->code))
            ->assertOk()
            ->assertSee('id="materialMethodPreview"', false)
            ->assertSee('data-method="fix"', false)
            ->assertSee('data-method="area"', false)
            ->assertDontSee('name="usage_method"', false)
            ->assertDontSee('type="radio"', false);
    }
}
