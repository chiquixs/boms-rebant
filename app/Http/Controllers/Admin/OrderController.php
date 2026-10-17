<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Platform;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductGroup;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    // ========== TAHAP 1: BACA DATA ==========
    public function index(Request $request)
    {
        $orders = Order::with('platform')
            ->when($request->filled('search'), fn ($q) =>
                $q->where('platform_order_no', 'ilike', '%' . $request->search . '%'))
            ->when($request->filled('platform'), fn ($q) =>
                $q->where('platform_id', $request->platform))
            ->when($request->filled('status'), fn ($q) =>
                $q->where('status', $request->status))
            ->orderByDesc('order_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        // Hitung jumlah per status untuk kartu ringkasan
        $counts = Order::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $platforms = Platform::orderBy('name')->get();

        $catalog = ProductGroup::with(['products' => fn ($q) =>
                $q->where('is_active', true)->orderBy('variant_name')])
            ->orderBy('name')                                   // <-- sesuaikan
            ->get()
            ->filter(fn ($g) => $g->products->isNotEmpty())
            ->map(fn ($g) => [
                'id'       => $g->id,
                'name'     => $g->name,                         // <-- sesuaikan
                'price'    => (float) $g->price,                // <-- sesuaikan
                'variants' => $g->products->map(fn ($p) => [
                    'id'    => $p->id,
                    'sku'   => $p->sku,
                    'name'  => $p->variant_name ?: $p->sku,
                    'image' => $p->image_url,
                ])->values(),
            ])
            ->values();

        return view('admin.orders.index', compact('orders', 'counts', 'platforms', 'catalog'));
    }

    public function show(Order $order)
    {
        $order->load(['platform', 'creator', 'items.product.group']);

        return view('admin.orders.show', compact('order'));
    }

    // ========== TAHAP 2: SIMPAN DATA ==========
    public function store(Request $request)
    {
        $data = $request->validate([
            'platform_id'       => ['required', 'exists:platforms,id'],
            'platform_order_no' => ['required', 'max:100',
                'unique:orders,platform_order_no,NULL,id,platform_id,' . $request->platform_id],
            'customer_name'     => ['required', 'max:150'],
            'order_date'        => ['required', 'date'],
            'notes'             => ['nullable', 'string'],

            'items'              => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required',
                Rule::exists('products', 'id')->where('is_active', true)],
            'items.*.qty'        => ['required', 'integer', 'min:1'],
        ], [
            'items.required'              => 'Tambahkan minimal satu produk.',
            'items.*.product_id.required' => 'Pilih produk dan varian di semua baris.',
            'items.*.product_id.exists'   => 'Ada varian yang tidak valid atau tidak aktif.',
        ]);

        DB::transaction(function () use ($data, $request) {

            // Harga diambil dari database (product_groups), bukan dari browser
            $products = Product::with('group')
                ->whereIn('id', collect($data['items'])->pluck('product_id'))
                ->get()
                ->keyBy('id');

            $total = 0;
            $rows  = [];

            foreach ($data['items'] as $item) {
                $price    = $products[$item['product_id']]->group->price;  // <-- sesuaikan
                $subtotal = $price * $item['qty'];
                $total   += $subtotal;

                $rows[] = [
                    'product_id' => $item['product_id'],
                    'qty'        => $item['qty'],
                    'unit_price' => $price,
                    'subtotal'   => $subtotal,
                ];
            }

            $order = Order::create([
                'platform_id'       => $data['platform_id'],
                'created_by'        => $request->user()->id,
                'platform_order_no' => $data['platform_order_no'],
                'customer_name'     => $data['customer_name'],
                'order_date'        => $data['order_date'],
                'status'            => Order::STATUS_NEW,
                'total_amount'      => $total,
                'notes'             => $data['notes'] ?? null,
            ]);

            $order->items()->createMany($rows);
        });

        return redirect()->route('admin.orders.index')
            ->with('success', 'Order berhasil ditambahkan.');
    }
}