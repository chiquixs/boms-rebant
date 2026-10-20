<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\RawMaterial;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index()
    {
        $materials = RawMaterial::orderBy('name')->get();

        return view('admin.materials.index', compact('materials'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['code'] = $this->generateCode();

        $material = RawMaterial::create($data);

        ActivityLog::record('create', 'material', $material->id, 'Bahan mentah baru ditambahkan.', [
            'title'    => $material->name,
            'subtitle' => $material->code . ' · ' . $material->unit,
        ]);

        return redirect()
            ->route('admin.materials.index')
            ->with('success', 'Bahan mentah berhasil ditambahkan.');
    }

    public function update(Request $request, RawMaterial $material)
    {
        $material->update($this->validated($request));

        ActivityLog::record('update', 'material', $material->id, 'Data bahan mentah diperbarui.', [
            'title'    => $material->name,
            'subtitle' => 'Safety ' . (float) $material->safety_stock . ' ' . $material->unit
                        . ' · Target ' . (float) $material->target_stock . ' ' . $material->unit,
        ]);

        return redirect()
            ->route('admin.materials.index')
            ->with('success', 'Bahan mentah berhasil diperbarui.');
    }

    public function destroy(RawMaterial $material)
    {
        $id       = $material->id;
        $name     = $material->name;
        $subtitle = $material->code . ' · ' . $material->unit;

        try {
            $material->delete();
        } catch (QueryException $e) {
            return redirect()
                ->route('admin.materials.index')
                ->with('error', 'Bahan mentah tidak bisa dihapus karena masih digunakan.');
        }

        ActivityLog::record('delete', 'material', $id, 'Bahan mentah dihapus.', [
            'title'    => $name,
            'subtitle' => $subtitle,
        ]);

        return redirect()
            ->route('admin.materials.index')
            ->with('success', 'Bahan mentah berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name'            => ['required', 'string', 'max:150'],
            'unit'            => ['required', 'in:lembar,mm,lt,pcs'],
            'material_type'   => ['required', 'in:SHEET,NON_SHEET'],
            'is_active'       => ['required', 'in:0,1'],
            'sheet_length_cm' => ['nullable', 'required_if:material_type,SHEET', 'numeric', 'gt:0', 'max:999999.99'],
            'sheet_width_cm'  => ['nullable', 'required_if:material_type,SHEET', 'numeric', 'gt:0', 'max:999999.99'],
            'current_stock'   => ['required', 'numeric', 'min:0', 'max:999999999.999'],
            'safety_stock'    => ['required', 'numeric', 'min:0', 'max:999999999.999'],
            'target_stock'    => ['required', 'numeric', 'min:0', 'max:999999999.999'],
        ], [
            'required'    => ':attribute wajib diisi.',
            'required_if' => ':attribute wajib diisi untuk material SHEET.',
            'numeric'     => ':attribute harus berupa angka.',
            'min'         => ':attribute tidak boleh negatif.',
            'gt'          => ':attribute harus lebih dari 0.',
            'max'         => ':attribute terlalu besar.',
            'in'          => ':attribute tidak valid.',
        ], [
            'name'            => 'Nama bahan',
            'unit'            => 'Satuan',
            'material_type'   => 'Tipe material',
            'is_active'       => 'Status',
            'sheet_length_cm' => 'Panjang sheet',
            'sheet_width_cm'  => 'Lebar sheet',
            'current_stock'   => 'Current stock',
            'safety_stock'    => 'Safety stock',
            'target_stock'    => 'Target stock',
        ]);

        $data['name']      = trim(preg_replace('/\s+/', ' ', $data['name']));
        $data['is_active'] = (bool) $data['is_active'];

        if ($data['material_type'] !== 'SHEET') {
            $data['sheet_length_cm'] = null;
            $data['sheet_width_cm']  = null;
        }

        return $data;
    }

    private function generateCode(): string
    {
        $last = RawMaterial::where('code', 'like', 'RM-%')
            ->orderByDesc('code')
            ->value('code');

        $next = $last ? ((int) substr($last, 3)) + 1 : 1;

        return 'RM-' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}