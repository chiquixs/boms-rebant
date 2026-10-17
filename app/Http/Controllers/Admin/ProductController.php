<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ProductGroup;
use Illuminate\Http\Request;

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

        // Status stok dihitung dari varian, jadi difilter setelah data diambil
        if ($request->filled('status')) {
            $groups = $groups->filter(fn ($g) => $g->stock_status === $request->status)->values();
        }

        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('groups', 'categories'));
    }
}