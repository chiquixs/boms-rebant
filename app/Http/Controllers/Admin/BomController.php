<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductGroup;
use App\Models\ProductMaterial;
use App\Models\RawMaterial;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class BomController extends Controller
{
    private function group(string $sku): ProductGroup
    {
        return ProductGroup::where('code', $sku)->firstOrFail();
    }

    public function index(string $sku)
    {
        $group = ProductGroup::with(['category', 'products', 'materials.rawMaterial'])
            ->where('code', $sku)->firstOrFail();

        $rawMaterials = RawMaterial::where('is_active', true)->orderBy('name')->get();

        return view('admin.products.bom', compact('group', 'rawMaterials'));
    }

    public function store(Request $request, string $sku)
    {
        $group = $this->group($sku);

        $group->materials()->create($this->payload($request, $group));

        return redirect()->route('admin.products.bom', $group->code)
            ->with('success', 'Bahan berhasil ditambahkan ke BOM.');
    }

    public function update(Request $request, string $sku, ProductMaterial $material)
    {
        $group = $this->group($sku);
        abort_unless($material->group_id === $group->id, 404);

        $material->update($this->payload($request, $group, $material));

        return redirect()->route('admin.products.bom', $group->code)
            ->with('success', 'Bahan di BOM berhasil diperbarui.');
    }

    public function destroy(string $sku, ProductMaterial $material)
    {
        $group = $this->group($sku);
        abort_unless($material->group_id === $group->id, 404);

        $material->delete();

        return redirect()->route('admin.products.bom', $group->code)
            ->with('success', 'Bahan dihapus dari BOM.');
    }

    private function payload(Request $request, ProductGroup $group, ?ProductMaterial $current = null): array
    {
        $data = $request->validate([
            'raw_material_id' => [
                'required',
                'exists:raw_materials,id',
                Rule::unique('product_materials', 'raw_material_id')
                    ->where('group_id', $group->id)
                    ->ignore($current?->id),
            ],
            'usage_method'    => ['required', 'in:fix,area'],
            'quantity'        => ['required_if:usage_method,fix', 'nullable', 'numeric', 'gt:0'],
            'piece_length_cm' => ['required_if:usage_method,area', 'nullable', 'numeric', 'gt:0'],
            'piece_width_cm'  => ['required_if:usage_method,area', 'nullable', 'numeric', 'gt:0'],
            'piece_count'     => ['required_if:usage_method,area', 'nullable', 'integer', 'min:1', 'max:32767'],
            'waste_percent'   => ['required_if:usage_method,area', 'nullable', 'numeric', 'between:0,100'],
        ], [
            'raw_material_id.unique' => 'Bahan ini sudah ada di BOM produk tersebut.',
        ]);

        // Kebutuhan FIX
        if ($data['usage_method'] === 'fix') {
            return [
                'raw_material_id' => $data['raw_material_id'],
                'usage_method'    => 'FIXED',
                'qty_per_unit'    => $data['quantity'],
                'waste_percent'   => 0,
                'piece_length_cm' => null,
                'piece_width_cm'  => null,
                'piece_count'     => 1,
            ];
        }

        // Kebutuhan AREA: dihitung dari ukuran lembar bahan
        $material = RawMaterial::findOrFail($data['raw_material_id']);

        if (! $material->has_sheet_size) {
            throw ValidationException::withMessages([
                'raw_material_id' => 'Metode Area hanya untuk bahan yang punya ukuran lembar. Lengkapi ukuran lembar di Master Bahan Mentah.',
            ]);
        }

        $area  = $data['piece_length_cm'] * $data['piece_width_cm'] * $data['piece_count']
               * (1 + $data['waste_percent'] / 100);
        $sheet = $material->sheet_length_cm * $material->sheet_width_cm;

        return [
            'raw_material_id' => $data['raw_material_id'],
            'usage_method'    => 'AREA',
            'qty_per_unit'    => round($area / $sheet, 6),
            'waste_percent'   => $data['waste_percent'],
            'piece_length_cm' => $data['piece_length_cm'],
            'piece_width_cm'  => $data['piece_width_cm'],
            'piece_count'     => $data['piece_count'],
        ];
    }
}