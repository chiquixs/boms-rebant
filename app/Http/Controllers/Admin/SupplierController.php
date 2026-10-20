<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RawMaterial;
use App\Models\Supplier;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::with('materials.rawMaterial')
            ->orderBy('name')
            ->get();

        $activeCount   = $suppliers->where('is_active', true)->count();
        $inactiveCount = $suppliers->count() - $activeCount;

        // Hanya bahan aktif yang boleh dipasang ke supplier (konsisten dengan aturan bisnis bahan nonaktif)
        $rawMaterials = RawMaterial::where('is_active', true)->orderBy('name')->get();

        return view('admin.procurement.suppliers', compact(
            'suppliers',
            'activeCount',
            'inactiveCount',
            'rawMaterials'
        ));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($data) {
            $supplier = Supplier::create($data['supplier']);

            if (! empty($data['materials'])) {
                $supplier->materials()->createMany($data['materials']);
            }
        });

        return redirect()
            ->route('admin.procurement.suppliers')
            ->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($data, $supplier) {
            $supplier->update($data['supplier']);

            // Pendekatan simpel: ganti total daftar bahan supplier ini
            $supplier->materials()->delete();

            if (! empty($data['materials'])) {
                $supplier->materials()->createMany($data['materials']);
            }
        });

        return redirect()
            ->route('admin.procurement.suppliers')
            ->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier)
    {
        try {
            DB::transaction(function () use ($supplier) {
                $supplier->materials()->delete();
                $supplier->delete();
            });
        } catch (QueryException $e) {
            return redirect()
                ->route('admin.procurement.suppliers')
                ->with('error', 'Supplier tidak bisa dihapus karena masih direferensikan oleh purchase order.');
        }

        return redirect()
            ->route('admin.procurement.suppliers')
            ->with('success', 'Supplier berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:150'],
            'email'     => ['nullable', 'email', 'max:150'],
            'phone'     => ['nullable', 'string', 'max:30'],
            'address'   => ['nullable', 'string'],
            'is_active' => ['required', 'in:0,1'],

            'materials'                     => ['nullable', 'array'],
            'materials.*.raw_material_id'   => ['required_with:materials', 'exists:raw_materials,id'],
            'materials.*.unit_price'        => ['required_with:materials', 'numeric', 'min:0'],
            'materials.*.lead_time_days'    => ['nullable', 'integer', 'min:0'],
            'materials.*.min_order_qty'     => ['nullable', 'numeric', 'min:0'],
        ], [
            'required'        => ':attribute wajib diisi.',
            'required_with'   => ':attribute wajib diisi untuk setiap baris bahan.',
            'email'           => 'Format email tidak valid.',
            'numeric'         => ':attribute harus berupa angka.',
            'integer'         => ':attribute harus berupa bilangan bulat.',
            'min'             => ':attribute tidak boleh negatif.',
            'exists'          => 'Bahan baku tidak valid.',
        ], [
            'name' => 'Nama supplier',
        ]);

        // Satu bahan tidak boleh dipilih dua kali untuk supplier yang sama
        if (! empty($validated['materials'])) {
            $ids = array_column($validated['materials'], 'raw_material_id');

            if (count($ids) !== count(array_unique($ids))) {
                throw ValidationException::withMessages([
                    'materials' => 'Satu bahan tidak boleh dipilih lebih dari sekali untuk supplier yang sama.',
                ]);
            }
        }

        return [
            'supplier' => [
                'name'      => trim(preg_replace('/\s+/', ' ', $validated['name'])),
                'email'     => $validated['email'] ?? null,
                'phone'     => $validated['phone'] ?? null,
                'address'   => $validated['address'] ?? null,
                'is_active' => (bool) $validated['is_active'],
            ],
            'materials' => collect($validated['materials'] ?? [])->map(fn($m) => [
                'raw_material_id' => $m['raw_material_id'],
                'unit_price'      => $m['unit_price'],
                'lead_time_days'  => $m['lead_time_days'] ?? 0,
                'min_order_qty'   => $m['min_order_qty'] ?? 0,
            ])->all(),
        ];
    }
}
