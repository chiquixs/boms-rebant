<?php

namespace Tests\Feature\Produksi;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductGroup;
use App\Models\ProductionLog;
use App\Models\ProductionLogMaterial;
use App\Models\ProductionRequest;
use App\Models\ProductStockMovement;
use App\Models\RawMaterial;
use App\Models\RawMaterialMovement;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProductionExecutionTest extends TestCase
{
    private User $productionUser;

    private Product $product;

    private RawMaterial $mdf;

    private RawMaterial $paint;

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
            $table->text('notes')->nullable();
            $table->timestamp('ready_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('qty');
            $table->decimal('unit_price', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->string('production_method', 3)->default('MTS');
            $table->timestamps();
        });

        Schema::create('production_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->foreignId('order_item_id')->nullable()->constrained('order_items')->restrictOnDelete();
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('request_no', 30)->unique();
            $table->string('source', 20);
            $table->unsignedInteger('qty_requested');
            $table->unsignedInteger('qty_produced')->default(0);
            $table->string('status', 20)->default('WAITING');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('production_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('production_request_id')->constrained('production_requests');
            $table->foreignId('recorded_by')->constrained('users');
            $table->date('production_date');
            $table->unsignedInteger('qty_produced');
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['id', 'production_request_id'], 'production_logs_id_request_unique');
        });

        DB::statement('CREATE TRIGGER production_logs_qty_check BEFORE INSERT ON production_logs BEGIN SELECT CASE WHEN NEW.qty_produced <= 0 THEN RAISE(ABORT, "CHECK constraint failed: qty_produced > 0") END; END');

        Schema::create('production_log_materials', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('production_log_id');
            $table->unsignedBigInteger('production_request_id');
            $table->foreignId('raw_material_id')->constrained('raw_materials');
            $table->date('usage_date');
            $table->decimal('qty_used', 15, 3);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign(['production_log_id', 'production_request_id'], 'production_log_materials_log_request_foreign')
                ->references(['id', 'production_request_id'])
                ->on('production_logs');

            $table->unique(['production_log_id', 'raw_material_id'], 'production_log_materials_one_per_log_unique');
        });

        DB::statement('CREATE TRIGGER production_log_materials_qty_check BEFORE INSERT ON production_log_materials BEGIN SELECT CASE WHEN NEW.qty_used <= 0 THEN RAISE(ABORT, "CHECK constraint failed: qty_used > 0") END; END');

        Schema::create('product_stock_movements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('order_item_id')->nullable()->constrained('order_items');
            $table->foreignId('production_log_id')->nullable()->constrained('production_logs');
            $table->string('movement_type', 15);
            $table->integer('qty');
            $table->integer('qty_before');
            $table->integer('qty_after');
            $table->text('note')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('raw_material_movements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('raw_material_id')->constrained()->restrictOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('production_log_material_id')->nullable()->constrained('production_log_materials');
            $table->unsignedBigInteger('purchase_order_item_id')->nullable();
            $table->string('movement_type', 15);
            $table->decimal('qty', 12, 3);
            $table->decimal('qty_before', 12, 3);
            $table->decimal('qty_after', 12, 3);
            $table->text('note')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        $productionRole = Role::create(['name' => 'produksi']);
        $this->productionUser = User::factory()->create([
            'role_id' => $productionRole->id,
            'is_active' => true,
        ]);

        $category = Category::create(['name' => 'SIGN PERINGATAN']);
        $group = ProductGroup::create([
            'code' => 'D6-A',
            'name' => 'Sign Peringatan Hitam',
            'category_id' => $category->id,
            'production_method' => 'MTS',
            'price' => 12000,
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'group_id' => $group->id,
            'sku' => 'D6A-1',
            'variant_name' => 'CCTV',
            'current_stock' => 0,
            'min_stock' => 10,
            'target_stock' => 30,
            'is_active' => true,
        ]);

        $this->mdf = RawMaterial::create([
            'code' => 'MDF-6MM',
            'name' => 'mdf 6 mm',
            'unit' => 'lembar',
            'material_type' => 'SHEET',
            'sheet_length_cm' => 244,
            'sheet_width_cm' => 122,
            'current_stock' => 10,
            'safety_stock' => 2,
            'target_stock' => 10,
            'is_active' => true,
        ]);

        $this->paint = RawMaterial::create([
            'code' => 'PAINT-BL',
            'name' => 'cat hitam',
            'unit' => 'ml',
            'material_type' => 'NON_SHEET',
            'current_stock' => 500,
            'safety_stock' => 100,
            'target_stock' => 500,
            'is_active' => true,
        ]);
    }

    public function test_multiple_sessions_on_same_day_succeed_and_complete_production(): void
    {
        $pr = ProductionRequest::create([
            'product_id' => $this->product->id,
            'requested_by' => $this->productionUser->id,
            'request_no' => 'PR-00001',
            'source' => ProductionRequest::SOURCE_RESTOCK,
            'qty_requested' => 2,
            'qty_produced' => 0,
            'status' => ProductionRequest::STATUS_WAITING,
        ]);

        // Sesi 1: Simpan Progres 1 pcs di hari yang sama
        $this->actingAs($this->productionUser)
            ->postJson(route('produksi.queue.execute', $pr->request_no), [
                'qty_produced_this_session' => 1,
                'finish' => false,
                'usages' => [
                    ['raw_material_id' => $this->mdf->id, 'qty_used' => 1],
                    ['raw_material_id' => $this->paint->id, 'qty_used' => 0],
                ],
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $pr->refresh();
        $this->assertSame(ProductionRequest::STATUS_IN_PROCESS, $pr->status);
        $this->assertSame(1, $pr->qty_produced);
        $this->assertSame(1, ProductionLog::where('production_request_id', $pr->id)->count());
        // Bahan bernilai 0 tidak disisipkan agar aman terhadap CHECK (qty_used > 0)
        $this->assertSame(1, ProductionLogMaterial::where('production_request_id', $pr->id)->count());

        // Sesi 2: Selesaikan sisa 1 pcs di hari yang sama dengan bahan MDF yang sama
        $this->actingAs($this->productionUser)
            ->postJson(route('produksi.queue.execute', $pr->request_no), [
                'qty_produced_this_session' => 1,
                'finish' => true,
                'usages' => [
                    ['raw_material_id' => $this->mdf->id, 'qty_used' => 1],
                    ['raw_material_id' => $this->paint->id, 'qty_used' => 20],
                ],
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $pr->refresh();
        $this->assertSame(ProductionRequest::STATUS_COMPLETED, $pr->status);
        $this->assertNotNull($pr->completed_at);
        $this->assertSame(2, $pr->qty_produced);
        $this->assertSame(2, ProductionLog::where('production_request_id', $pr->id)->count());
        $this->assertSame(3, ProductionLogMaterial::where('production_request_id', $pr->id)->count());
        $this->assertSame(3, RawMaterialMovement::count());
        $this->assertSame(2, ProductStockMovement::count());
        $this->assertSame(2, $this->product->fresh()->current_stock);
        $this->assertEquals(8.0, (float) $this->mdf->fresh()->current_stock);
        $this->assertEquals(480.0, (float) $this->paint->fresh()->current_stock);
    }

    public function test_finish_is_rejected_when_total_produced_is_less_than_requested(): void
    {
        $pr = ProductionRequest::create([
            'product_id' => $this->product->id,
            'requested_by' => $this->productionUser->id,
            'request_no' => 'PR-00002',
            'source' => ProductionRequest::SOURCE_RESTOCK,
            'qty_requested' => 10,
            'qty_produced' => 0,
            'status' => ProductionRequest::STATUS_WAITING,
        ]);

        // Coba langsung Selesaikan Produksi dengan 9 dari 10 pcs
        $response = $this->actingAs($this->productionUser)
            ->postJson(route('produksi.queue.execute', $pr->request_no), [
                'qty_produced_this_session' => 9,
                'finish' => true,
                'usages' => [
                    ['raw_material_id' => $this->mdf->id, 'qty_used' => 1],
                ],
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['qty_produced_this_session']);

        $this->assertStringContainsString('kurang dari jumlah yang diminta', (string) $response->json('message'));

        $pr->refresh();
        $this->assertSame(ProductionRequest::STATUS_WAITING, $pr->status);
        $this->assertNull($pr->completed_at);
        $this->assertSame(0, $pr->qty_produced);
        $this->assertSame(0, ProductionLog::count());
        $this->assertEquals(10.0, (float) $this->mdf->fresh()->current_stock);

        // Simpan progres 5 pcs dulu, lalu coba Selesaikan Produksi dengan +4 pcs (total 9 < 10)
        $this->actingAs($this->productionUser)
            ->postJson(route('produksi.queue.execute', $pr->request_no), [
                'qty_produced_this_session' => 5,
                'finish' => false,
                'usages' => [
                    ['raw_material_id' => $this->mdf->id, 'qty_used' => 1],
                ],
            ])
            ->assertOk();

        $this->actingAs($this->productionUser)
            ->postJson(route('produksi.queue.execute', $pr->request_no), [
                'qty_produced_this_session' => 4,
                'finish' => true,
                'usages' => [
                    ['raw_material_id' => $this->mdf->id, 'qty_used' => 1],
                ],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['qty_produced_this_session']);

        $pr->refresh();
        $this->assertSame(ProductionRequest::STATUS_IN_PROCESS, $pr->status);
        $this->assertSame(5, $pr->qty_produced);
    }

    public function test_finish_with_zero_session_qty_succeeds_when_target_already_reached(): void
    {
        $pr = ProductionRequest::create([
            'product_id' => $this->product->id,
            'requested_by' => $this->productionUser->id,
            'request_no' => 'PR-00003',
            'source' => ProductionRequest::SOURCE_RESTOCK,
            'qty_requested' => 4,
            'qty_produced' => 0,
            'status' => ProductionRequest::STATUS_WAITING,
        ]);

        // Simpan progres penuh 4 pcs
        $this->actingAs($this->productionUser)
            ->postJson(route('produksi.queue.execute', $pr->request_no), [
                'qty_produced_this_session' => 4,
                'finish' => false,
                'usages' => [
                    ['raw_material_id' => $this->mdf->id, 'qty_used' => 1],
                ],
            ])
            ->assertOk();

        // Selesaikan status dengan 0 pcs karena target sudah terpenuhi (tanpa membuat log bernilai 0)
        $this->actingAs($this->productionUser)
            ->postJson(route('produksi.queue.execute', $pr->request_no), [
                'qty_produced_this_session' => 0,
                'finish' => true,
                'usages' => [
                    ['raw_material_id' => $this->mdf->id, 'qty_used' => 0],
                ],
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $pr->refresh();
        $this->assertSame(ProductionRequest::STATUS_COMPLETED, $pr->status);
        $this->assertSame(4, $pr->qty_produced);
        $this->assertSame(1, ProductionLog::where('production_request_id', $pr->id)->count());
    }

    public function test_execution_modal_contains_under_target_validation_message_without_confirm_bypass(): void
    {
        $this->withoutVite()
            ->actingAs($this->productionUser)
            ->get(route('produksi.queue.index'))
            ->assertOk()
            ->assertSee('kurang dari jumlah yang diminta')
            ->assertDontSee('Tetap selesaikan produksi?');
    }
}
