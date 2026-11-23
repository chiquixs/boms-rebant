<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
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

    private function log(ProductGroup $group, string $action, string $activity, string $description, ?string $material = null): void
    {
        ActivityLog::record($action, 'product', $group->id, $description, [
            'activity' => $activity,
            'title' => $group->code,
            'subtitle' => $material ? $group->name.' · '.$material : $group->name,
        ]);
    }

    public function index(string $sku)
    {
        $group = ProductGroup::with(['category', 'products', 'materials.rawMaterial'])
            ->where('code', $sku)->firstOrFail();

        $usedIds = $group->materials->pluck('raw_material_id');

        // Bahan yang sudah dipakai di BOM tetap muncul walau sudah dinonaktifkan
        $rawMaterials = RawMaterial::where('is_active', true)
            ->orWhereIn('id', $usedIds)
            ->orderBy('name')
            ->get();

        return view('admin.products.bom', compact('group', 'rawMaterials'));
    }

    public function store(Request $request, string $sku)
    {
        $group = $this->group($sku);
        $material = $group->materials()->create($this->payload($request, $group));
        $material->load('rawMaterial');

        $this->log($group, 'create', 'Menambah Bahan BOM', 'Bahan ditambahkan ke BOM.', $material->rawMaterial->name);

        return redirect()->route('admin.products.bom', $group->code)
            ->with('success', 'Bahan berhasil ditambahkan ke BOM.');
    }

    public function update(Request $request, string $sku, ProductMaterial $material)
    {
        $group = $this->group($sku);
        abort_unless($material->group_id === $group->id, 404);

        $material->update($this->payload($request, $group, $material));
        $material->load('rawMaterial');

        $this->log($group, 'update', 'Mengubah Bahan BOM', 'Bahan di BOM diperbarui.', $material->rawMaterial->name);

        return redirect()->route('admin.products.bom', $group->code)
            ->with('success', 'Bahan di BOM berhasil diperbarui.');
    }

    public function destroy(string $sku, ProductMaterial $material)
    {
        $group = $this->group($sku);
        abort_unless($material->group_id === $group->id, 404);

        $name = $material->rawMaterial?->name;

        $material->delete();

        $this->log($group, 'delete', 'Menghapus Bahan BOM', 'Bahan dihapus dari BOM.', $name);

        return redirect()->route('admin.products.bom', $group->code)
            ->with('success', 'Bahan dihapus dari BOM.');
    }

    private function payload(Request $request, ProductGroup $group, ?ProductMaterial $current = null): array
    {
        $base = $request->validate([
            'raw_material_id' => [
                'required',
                'exists:raw_materials,id',
                Rule::unique('product_materials', 'raw_material_id')
                    ->where('group_id', $group->id)
                    ->ignore($current?->id),
            ],
        ], [
            'raw_material_id.required' => 'Silakan pilih bahan mentah.',
            'raw_material_id.exists' => 'Bahan mentah yang dipilih tidak valid.',
            'raw_material_id.unique' => 'Bahan ini sudah ada di BOM produk tersebut.',
        ]);

        $material = RawMaterial::findOrFail($base['raw_material_id']);

        if (! $material->isSheet()) {
            $data = $request->validate([
                'quantity' => ['required', 'numeric', 'gt:0'],
            ], [
                'quantity.required' => 'Jumlah kebutuhan bahan wajib diisi.',
                'quantity.numeric' => 'Jumlah kebutuhan bahan harus berupa angka.',
                'quantity.gt' => 'Jumlah kebutuhan bahan harus lebih besar dari 0.',
            ]);

            return [
                'raw_material_id' => $material->id,
                'usage_method' => 'FIXED',
                'qty_per_unit' => $data['quantity'],
                'waste_percent' => 0,
                'piece_length_cm' => null,
                'piece_width_cm' => null,
                'piece_count' => 1,
            ];
        }

        if (! $material->has_sheet_size) {
            throw ValidationException::withMessages([
                'raw_material_id' => 'Bahan tipe lembar (Area) belum memiliki ukuran lembar. Lengkapi ukuran lembar di Master Bahan Mentah.',
            ]);
        }

        $data = $request->validate([
            'piece_length_cm' => ['required', 'numeric', 'gt:0'],
            'piece_width_cm' => ['required', 'numeric', 'gt:0'],
            'piece_count' => ['required', 'integer', 'min:1', 'max:32767'],
            'waste_percent' => ['required', 'numeric', 'between:0,100'],
        ], [
            'piece_length_cm.required' => 'Panjang potongan wajib diisi.',
            'piece_length_cm.gt' => 'Panjang potongan harus lebih besar dari 0.',
            'piece_width_cm.required' => 'Lebar potongan wajib diisi.',
            'piece_width_cm.gt' => 'Lebar potongan harus lebih besar dari 0.',
            'piece_count.required' => 'Jumlah potongan wajib diisi.',
            'piece_count.min' => 'Jumlah potongan minimal 1.',
            'waste_percent.required' => 'Waste / toleransi wajib diisi.',
            'waste_percent.between' => 'Waste / toleransi harus antara 0 sampai 100%.',
        ]);

        $area = $data['piece_length_cm'] * $data['piece_width_cm'] * $data['piece_count']
            * (1 + $data['waste_percent'] / 100);
        $sheet = $material->sheet_length_cm * $material->sheet_width_cm;

        return [
            'raw_material_id' => $material->id,
            'usage_method' => 'AREA',
            'qty_per_unit' => round($area / $sheet, 6),
            'waste_percent' => $data['waste_percent'],
            'piece_length_cm' => $data['piece_length_cm'],
            'piece_width_cm' => $data['piece_width_cm'],
            'piece_count' => $data['piece_count'],
        ];
    }
}
