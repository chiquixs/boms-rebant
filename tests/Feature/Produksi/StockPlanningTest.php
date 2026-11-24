<?php

namespace Tests\Feature\Produksi;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemAllocation;
use App\Models\Platform;
use App\Models\Product;
use App\Models\ProductGroup;
use App\Models\ProductionLog;
use App\Models\ProductionRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class StockPlanningTest extends TestCase
{
    private User $productionUser;

    private Category $category;

    private ProductGroup $mtsGroup;

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

        Schema::create('order_item_allocations', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('order_item_id')->unique();
            $table->unsignedBigInteger('product_id');
            $table->unsignedInteger('qty_allocated');
            $table->string('status', 20)->default('RESERVED');
            $table->timestamp('consumed_at')->nullable();
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
        });

        $productionRole = Role::create(['name' => 'produksi']);
        $this->productionUser = User::factory()->create([
            'role_id' => $productionRole->id,
            'is_active' => true,
        ]);

        $this->category = Category::create(['name' => 'Wall Decor']);
        $this->mtsGroup = ProductGroup::create([
            'code' => 'WD-MTS',
            'name' => 'Sign Peringatan',
            'category_id' => $this->category->id,
            'production_method' => 'MTS',
            'price' => 25000,
            'is_active' => true,
        ]);
    }

    public function test_products_endpoint_returns_active_mts_variants_with_reserved_and_available_stock(): void
    {
        $activeVariant = Product::create([
            'group_id' => $this->mtsGroup->id,
            'sku' => 'WD-1',
            'variant_name' => 'CCTV',
            'current_stock' => 15,
            'min_stock' => 10,
            'target_stock' => 30,
            'is_active' => true,
        ]);

        Product::create([
            'group_id' => $this->mtsGroup->id,
            'sku' => 'WD-INACTIVE',
            'variant_name' => 'Nonaktif',
            'current_stock' => 5,
            'min_stock' => 10,
            'target_stock' => 30,
            'is_active' => false,
        ]);

        $mtoGroup = ProductGroup::create([
            'code' => 'WD-MTO',
            'name' => 'Custom Frame',
            'category_id' => $this->category->id,
            'production_method' => 'MTO',
            'price' => 50000,
            'is_active' => true,
        ]);

        Product::create([
            'group_id' => $mtoGroup->id,
            'sku' => 'MTO-1',
            'variant_name' => 'Custom',
            'current_stock' => 0,
            'is_active' => true,
        ]);

        $platform = Platform::create(['name' => 'Shopee']);
        $order = Order::create([
            'platform_id' => $platform->id,
            'created_by' => $this->productionUser->id,
            'platform_order_no' => 'ORD-001',
            'customer_name' => 'Budi',
            'order_date' => now()->toDateString(),
            'status' => 'WAITING_PRODUCTION',
            'total_amount' => 150000,
        ]);

        $reservedItem = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $activeVariant->id,
            'qty' => 6,
            'unit_price' => 25000,
            'subtotal' => 150000,
            'production_method' => 'MTS',
        ]);

        $consumedItem = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $activeVariant->id,
            'qty' => 4,
            'unit_price' => 25000,
            'subtotal' => 100000,
            'production_method' => 'MTS',
        ]);

        OrderItemAllocation::create([
            'order_item_id' => $reservedItem->id,
            'product_id' => $activeVariant->id,
            'qty_allocated' => 6,
            'status' => OrderItemAllocation::STATUS_RESERVED,
        ]);

        OrderItemAllocation::create([
            'order_item_id' => $consumedItem->id,
            'product_id' => $activeVariant->id,
            'qty_allocated' => 4,
            'status' => OrderItemAllocation::STATUS_CONSUMED,
            'consumed_at' => now(),
        ]);

        $response = $this->actingAs($this->productionUser)
            ->getJson(route('produksi.planning.products'))
            ->assertOk();

        $response->assertJsonCount(1);
        $response->assertJsonFragment([
            'id' => $activeVariant->id,
            'sku' => 'WD-1',
            'current_stock' => 15,
            'reserved_stock' => 6,
            'available_stock' => 9,
        ]);
    }

    public function test_requests_endpoint_returns_only_active_restock_requests_for_active_mts_products(): void
    {
        $mtsVariant = Product::create([
            'group_id' => $this->mtsGroup->id,
            'sku' => 'WD-1',
            'variant_name' => 'CCTV',
            'current_stock' => 5,
            'min_stock' => 10,
            'target_stock' => 30,
            'is_active' => true,
        ]);

        $inactiveVariant = Product::create([
            'group_id' => $this->mtsGroup->id,
            'sku' => 'WD-2',
            'variant_name' => 'Off',
            'current_stock' => 5,
            'min_stock' => 10,
            'target_stock' => 30,
            'is_active' => false,
        ]);

        $mtoGroup = ProductGroup::create([
            'code' => 'WD-MTO',
            'name' => 'Custom Frame',
            'category_id' => $this->category->id,
            'production_method' => 'MTO',
            'price' => 50000,
            'is_active' => true,
        ]);

        $mtoVariant = Product::create([
            'group_id' => $mtoGroup->id,
            'sku' => 'MTO-1',
            'variant_name' => 'Custom',
            'current_stock' => 0,
            'is_active' => true,
        ]);

        $platform = Platform::create(['name' => 'Tokopedia']);
        $order = Order::create([
            'platform_id' => $platform->id,
            'created_by' => $this->productionUser->id,
            'platform_order_no' => 'ORD-002',
            'customer_name' => 'Siti',
            'order_date' => now()->toDateString(),
            'status' => 'WAITING_PRODUCTION',
            'total_amount' => 75000,
        ]);

        $mtsOrderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $mtsVariant->id,
            'qty' => 5,
            'unit_price' => 25000,
            'subtotal' => 125000,
            'production_method' => 'MTS',
        ]);

        $mtoOrderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $mtoVariant->id,
            'qty' => 2,
            'unit_price' => 50000,
            'subtotal' => 100000,
            'production_method' => 'MTO',
        ]);

        $activeRestock = ProductionRequest::create([
            'product_id' => $mtsVariant->id,
            'requested_by' => $this->productionUser->id,
            'request_no' => 'PR-00001',
            'source' => ProductionRequest::SOURCE_RESTOCK,
            'qty_requested' => 10,
            'qty_produced' => 3,
            'status' => ProductionRequest::STATUS_IN_PROCESS,
        ]);

        ProductionLog::create([
            'production_request_id' => $activeRestock->id,
            'recorded_by' => $this->productionUser->id,
            'production_date' => now()->toDateString(),
            'qty_produced' => 3,
        ]);

        // Request ORDER (baik MTS maupun MTO), COMPLETED, dan produk nonaktif tidak boleh masuk
        ProductionRequest::create([
            'product_id' => $mtsVariant->id,
            'order_item_id' => $mtsOrderItem->id,
            'requested_by' => $this->productionUser->id,
            'request_no' => 'PR-00002',
            'source' => ProductionRequest::SOURCE_ORDER,
            'qty_requested' => 5,
            'qty_produced' => 0,
            'status' => ProductionRequest::STATUS_WAITING,
        ]);

        ProductionRequest::create([
            'product_id' => $mtoVariant->id,
            'order_item_id' => $mtoOrderItem->id,
            'requested_by' => $this->productionUser->id,
            'request_no' => 'PR-00003',
            'source' => ProductionRequest::SOURCE_ORDER,
            'qty_requested' => 2,
            'qty_produced' => 0,
            'status' => ProductionRequest::STATUS_IN_PROCESS,
        ]);

        ProductionRequest::create([
            'product_id' => $mtsVariant->id,
            'requested_by' => $this->productionUser->id,
            'request_no' => 'PR-00004',
            'source' => ProductionRequest::SOURCE_RESTOCK,
            'qty_requested' => 8,
            'qty_produced' => 8,
            'status' => ProductionRequest::STATUS_COMPLETED,
        ]);

        ProductionRequest::create([
            'product_id' => $inactiveVariant->id,
            'requested_by' => $this->productionUser->id,
            'request_no' => 'PR-00005',
            'source' => ProductionRequest::SOURCE_RESTOCK,
            'qty_requested' => 6,
            'qty_produced' => 0,
            'status' => ProductionRequest::STATUS_WAITING,
        ]);

        $response = $this->actingAs($this->productionUser)
            ->getJson(route('produksi.planning.requests'))
            ->assertOk();

        $response->assertJsonCount(1);
        $response->assertJsonFragment([
            'id' => $activeRestock->id,
            'request_no' => 'PR-00001',
            'source' => ProductionRequest::SOURCE_RESTOCK,
            'qty_requested' => 10,
            'qty_produced' => 3,
            'status' => ProductionRequest::STATUS_IN_PROCESS,
        ]);
    }

    public function test_store_ignores_order_requests_and_accounts_for_reserved_stock(): void
    {
        $variant = Product::create([
            'group_id' => $this->mtsGroup->id,
            'sku' => 'WD-1',
            'variant_name' => 'CCTV',
            'current_stock' => 20,
            'min_stock' => 10,
            'target_stock' => 20,
            'is_active' => true,
        ]);

        $platform = Platform::create(['name' => 'Shopee']);
        $order = Order::create([
            'platform_id' => $platform->id,
            'created_by' => $this->productionUser->id,
            'platform_order_no' => 'ORD-003',
            'customer_name' => 'Andi',
            'order_date' => now()->toDateString(),
            'status' => 'WAITING_PRODUCTION',
            'total_amount' => 200000,
        ]);

        $reservedItem = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $variant->id,
            'qty' => 8,
            'unit_price' => 25000,
            'subtotal' => 200000,
            'production_method' => 'MTS',
        ]);

        OrderItemAllocation::create([
            'order_item_id' => $reservedItem->id,
            'product_id' => $variant->id,
            'qty_allocated' => 8,
            'status' => OrderItemAllocation::STATUS_RESERVED,
        ]);

        // Request ORDER aktif (8 pcs) tidak boleh dianggap mengisi target stok gudang
        $pendingOrderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $variant->id,
            'qty' => 8,
            'unit_price' => 25000,
            'subtotal' => 200000,
            'production_method' => 'MTS',
        ]);

        ProductionRequest::create([
            'product_id' => $variant->id,
            'order_item_id' => $pendingOrderItem->id,
            'requested_by' => $this->productionUser->id,
            'request_no' => 'PR-00010',
            'source' => ProductionRequest::SOURCE_ORDER,
            'qty_requested' => 8,
            'qty_produced' => 0,
            'status' => ProductionRequest::STATUS_WAITING,
        ]);

        // available = 20 - 8 = 12 -> base = 20 - 12 = 8 -> rec = 8 -> request RESTOCK boleh dibuat
        $this->actingAs($this->productionUser)
            ->postJson(route('produksi.planning.store'), [
                'product_id' => $variant->id,
                'qty_requested' => 8,
                'source' => ProductionRequest::SOURCE_RESTOCK,
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        // Setelah RESTOCK 8 dibuat, rec menjadi 0 -> request kedua harus ditolak
        $this->actingAs($this->productionUser)
            ->postJson(route('produksi.planning.store'), [
                'product_id' => $variant->id,
                'qty_requested' => 4,
                'source' => ProductionRequest::SOURCE_RESTOCK,
            ])
            ->assertUnprocessable();
    }

    public function test_planning_page_renders_variant_total_and_restock_active_labels(): void
    {
        $this->withoutVite()
            ->actingAs($this->productionUser)
            ->get(route('produksi.planning.index'))
            ->assertOk()
            ->assertSee('Total Varian MTS')
            ->assertSee('Request RESTOCK (WAITING / IN_PROCESS)')
            ->assertSee('id="spStatNeedNote"', false)
            ->assertSee('id="spTabHint"', false);
    }
}
