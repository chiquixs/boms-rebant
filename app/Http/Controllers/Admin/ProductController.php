<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $groups = ProductGroup::with([
                'category',
                'products' => fn ($q) => $q->orderBy('variant_name'),
            ])
            ->withCount('materials')
            ->when($request->filled('search'), fn ($q) =>
                $q->where(fn ($w) => $w
                    ->where('name', 'ilike', '%' . $request->search . '%')
                    ->orWhere('code', 'ilike', '%' . $request->search . '%')))
            ->when($request->filled('category'), fn ($q) =>
                $q->where('category_id', $request->category))
            ->when($request->filled('type'), fn ($q) =>
                $q->where('production_method', strtoupper($request->type)))
            ->orderBy('name')
            ->get();

        if ($request->filled('status')) {
            $groups = $groups->filter(fn ($g) => $g->stock_status === $request->status)->values();
        }

        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('groups', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'              => ['required', 'max:50', 'unique:product_groups,code'],
            'name'              => ['required', 'max:150'],
            'category_id'       => ['nullable', 'exists:categories,id'],
            'price'             => ['required', 'numeric', 'min:0'],
            'length_cm'         => ['nullable', 'numeric', 'min:0'],
            'width_cm'          => ['nullable', 'numeric', 'min:0'],
            'production_method' => ['required', 'in:mts,mto'],

            'variants'                 => ['required', 'array', 'min:1'],
            'variants.*.sku'           => ['required', 'max:50', 'distinct', 'unique:products,sku'],
            'variants.*.variant_name'  => ['nullable', 'max:100'],
            'variants.*.current_stock' => ['nullable', 'integer', 'min:0'],
            'variants.*.min_stock'     => ['nullable', 'integer', 'min:0'],
            'variants.*.target_stock'  => ['nullable', 'integer', 'min:0'],
            'variants.*.image'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'code.unique'             => 'SKU Product sudah dipakai.',
            'variants.*.sku.required' => 'SKU Variant wajib diisi di semua varian.',
            'variants.*.sku.unique'   => 'Ada SKU Variant yang sudah dipakai.',
            'variants.*.sku.distinct' => 'SKU Variant tidak boleh sama dalam satu produk.',
            'variants.*.image.max'    => 'Ukuran foto varian maksimal 2 MB.',
        ]);

        $variants = array_values($data['variants']);
        $isMto    = $data['production_method'] === 'mto';

        $names = collect($variants)->pluck('variant_name')->filter()->map(fn ($n) => mb_strtolower($n));

        if ($names->count() !== $names->unique()->count()) {
            throw ValidationException::withMessages([
                'variants' => 'Nama varian tidak boleh sama dalam satu produk.',
            ]);
        }

        if (! $isMto) {
            foreach ($variants as $v) {
                $min    = $v['min_stock'] ?? null;
                $target = $v['target_stock'] ?? null;

                if ($min !== null && $target !== null && (int) $target <= (int) $min) {
                    throw ValidationException::withMessages([
                        'variants' => 'Target stock harus lebih besar dari minimum stock.',
                    ]);
                }
            }
        }

        $disk     = config('filesystems.product_image_disk');
        $uploaded = [];

        try {
            DB::transaction(function () use ($request, $data, $variants, $isMto, $disk, &$uploaded) {

                $group = ProductGroup::create([
                    'code'              => $data['code'],
                    'name'              => $data['name'],
                    'category_id'       => $data['category_id'] ?? null,
                    'price'             => $data['price'],
                    'length_cm'         => $data['length_cm'] ?? null,
                    'width_cm'          => $data['width_cm'] ?? null,
                    'production_method' => strtoupper($data['production_method']),
                    'is_active'         => true,
                ]);

                foreach ($variants as $i => $v) {

                    $imagePath = null;
                    $file      = $request->file("variants")[array_keys($data['variants'])[$i]]['image'] ?? null;

                    if ($file) {
                        $imagePath  = $file->store('variants/' . $v['sku'], $disk);
                        $uploaded[] = $imagePath;
                    }

                    Product::create([
                        'group_id'      => $group->id,
                        'sku'           => $v['sku'],
                        'variant_name'  => $v['variant_name'] ?? null,
                        'image_path'    => $imagePath,
                        'current_stock' => $isMto ? 0 : (int) ($v['current_stock'] ?? 0),
                        'min_stock'     => $isMto ? null : ($v['min_stock'] ?? null),
                        'target_stock'  => $isMto ? null : ($v['target_stock'] ?? null),
                        'is_active'     => true,
                    ]);

                    if ($i === 0 && $imagePath) {
                        $group->update(['image_path' => $imagePath]);
                    }
                }

                ActivityLog::record('create', 'product', $group->id, 'Produk baru ditambahkan.', [
                    'title'    => $group->code,
                    'subtitle' => $group->name,
                ]);
            });
        } catch (\Throwable $e) {
            foreach ($uploaded as $path) {
                Storage::disk($disk)->delete($path);
            }

            throw $e;
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function update(Request $request, ProductGroup $group)
    {
        $rules = [
            'code'              => ['required', 'max:50', Rule::unique('product_groups', 'code')->ignore($group->id)],
            'name'              => ['required', 'max:150'],
            'category_id'       => ['nullable', 'exists:categories,id'],
            'price'             => ['required', 'numeric', 'min:0'],
            'length_cm'         => ['nullable', 'numeric', 'min:0'],
            'width_cm'          => ['nullable', 'numeric', 'min:0'],
            'production_method' => ['required', 'in:mts,mto'],

            'variants'                 => ['required', 'array', 'min:1'],
            'variants.*.id'            => ['nullable', 'integer'],
            'variants.*.variant_name'  => ['nullable', 'max:100'],
            'variants.*.current_stock' => ['nullable', 'integer', 'min:0'],
            'variants.*.min_stock'     => ['nullable', 'integer', 'min:0'],
            'variants.*.target_stock'  => ['nullable', 'integer', 'min:0'],
            'variants.*.image'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];

        $ownIds = $group->products()->pluck('id')->all();

        foreach (array_keys($request->input('variants', [])) as $k) {
            $rules["variants.$k.sku"] = [
                'required', 'max:50', 'distinct',
                Rule::unique('products', 'sku')->whereNotIn('id', $ownIds),
            ];
        }

        $validator = Validator::make($request->all(), $rules, [
            'code.unique'             => 'SKU Product sudah dipakai.',
            'variants.*.sku.required' => 'SKU Variant wajib diisi di semua varian.',
            'variants.*.sku.unique'   => 'Ada SKU Variant yang sudah dipakai.',
            'variants.*.sku.distinct' => 'SKU Variant tidak boleh sama dalam satu produk.',
            'variants.*.image.max'    => 'Ukuran foto varian maksimal 2 MB.',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        $data     = $validator->validated();
        $variants = $data['variants'];
        $isMto    = $data['production_method'] === 'mto';

        $names = collect($variants)->pluck('variant_name')->filter()->map(fn ($n) => mb_strtolower($n));

        if ($names->count() !== $names->unique()->count()) {
            return back()->with('error', 'Nama varian tidak boleh sama dalam satu produk.');
        }

        if (! $isMto) {
            foreach ($variants as $v) {
                $min    = $v['min_stock'] ?? null;
                $target = $v['target_stock'] ?? null;

                if ($min !== null && $target !== null && (int) $target <= (int) $min) {
                    return back()->with('error', 'Target stock harus lebih besar dari minimum stock.');
                }
            }
        }

        $existing     = $group->products()->get()->keyBy('id');
        $submittedIds = collect($variants)->pluck('id')->filter()->map(fn ($id) => (int) $id);

        if ($submittedIds->contains(fn ($id) => ! $existing->has($id))) {
            return back()->with('error', 'Data varian tidak valid.');
        }

        $removed = $existing->keys()->diff($submittedIds);

        if ($removed->isNotEmpty() && OrderItem::whereIn('product_id', $removed)->exists()) {
            return back()->with('error', 'Ada varian yang sudah dipakai di order, sehingga tidak bisa dihapus.');
        }

        $disk     = config('filesystems.product_image_disk');
        $uploaded = [];
        $oldFiles = [];

        try {
            DB::transaction(function () use ($request, $group, $data, $variants, $isMto, $existing, $removed, $disk, &$uploaded, &$oldFiles) {

                // 1. Hapus varian yang dibuang
                foreach ($removed as $id) {
                    if ($existing[$id]->image_path) {
                        $oldFiles[] = $existing[$id]->image_path;
                    }
                }
                if ($removed->isNotEmpty()) {
                    Product::whereIn('id', $removed)->delete();
                }

                // 2. SKU/nama sementara agar pertukaran tidak bentrok constraint unik
                foreach ($existing as $row) {
                    if (! $removed->contains($row->id)) {
                        $row->forceFill([
                            'sku'          => 'TMP-' . $row->id . '-' . uniqid(),
                            'variant_name' => $row->variant_name ? 'TMP-' . uniqid() : null,
                        ])->saveQuietly();
                    }
                }

                // 3. Update atau tambah varian
                $firstImage = null;

                foreach ($variants as $k => $v) {

                    $product   = ! empty($v['id']) ? $existing[(int) $v['id']] : new Product(['group_id' => $group->id]);
                    $imagePath = $product->image_path;

                    $file = $request->file("variants.$k.image");

                    if ($file) {
                        if ($imagePath) {
                            $oldFiles[] = $imagePath;
                        }
                        $imagePath  = $file->store('variants/' . $v['sku'], $disk);
                        $uploaded[] = $imagePath;
                    }

                    $product->fill([
                        'sku'           => $v['sku'],
                        'variant_name'  => $v['variant_name'] ?? null,
                        'image_path'    => $imagePath,
                        'current_stock' => $isMto ? 0 : (int) ($v['current_stock'] ?? 0),
                        'min_stock'     => $isMto ? null : ($v['min_stock'] ?? null),
                        'target_stock'  => $isMto ? null : ($v['target_stock'] ?? null),
                        'is_active'     => true,
                    ])->save();

                    $firstImage ??= $imagePath;
                }

                // 4. Update produk induk
                $group->update([
                    'code'              => $data['code'],
                    'name'              => $data['name'],
                    'category_id'       => $data['category_id'] ?? null,
                    'price'             => $data['price'],
                    'length_cm'         => $data['length_cm'] ?? null,
                    'width_cm'          => $data['width_cm'] ?? null,
                    'production_method' => strtoupper($data['production_method']),
                    'image_path'        => $firstImage,
                ]);

                ActivityLog::record('update', 'product', $group->id, 'Informasi produk diperbarui.', [
                    'title'    => $group->code,
                    'subtitle' => $group->name,
                ]);
            });
        } catch (\Throwable $e) {
            foreach ($uploaded as $path) {
                Storage::disk($disk)->delete($path);
            }

            throw $e;
        }

        foreach (array_unique($oldFiles) as $path) {
            Storage::disk($disk)->delete($path);
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(ProductGroup $group)
    {
        $variantIds = $group->products()->pluck('id');

        if (OrderItem::whereIn('product_id', $variantIds)->exists()) {
            return back()->with('error', 'Produk tidak bisa dihapus karena sudah dipakai di order.');
        }

        $paths = $group->products()->pluck('image_path')
            ->push($group->image_path)->filter()->unique();

        $code = $group->code;
        $name = $group->name;
        $id   = $group->id;

        DB::transaction(function () use ($group, $id, $code, $name) {
            $group->products()->delete();
            $group->delete(); // BOM ikut terhapus lewat cascade

            ActivityLog::record('delete', 'product', $id, 'Produk dihapus.', [
                'title'    => $code,
                'subtitle' => $name,
            ]);
        });

        $disk = config('filesystems.product_image_disk');

        foreach ($paths as $path) {
            Storage::disk($disk)->delete($path);
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}