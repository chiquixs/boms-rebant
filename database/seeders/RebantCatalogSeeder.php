<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class RebantCatalogSeeder extends Seeder
{
    private int $adminId;

    /**
     * Mengisi kategori, bahan baku, produk induk, variasi (SKU), dan resep dari file CSV
     * di database/seeders/data. Hanya MENAMBAH data: baris yang sudah ada (kode/SKU sama) dilewati,
     * tidak ditimpa. Semua dalam satu transaksi: jika ada yang salah, tidak ada data yang masuk.
     */
    public function run(): void
    {
        $adminId = DB::table('users')
            ->join('roles', 'roles.id', '=', 'users.role_id')
            ->where('roles.name', 'admin')
            ->orderBy('users.id')
            ->value('users.id');

        if (! $adminId) {
            throw new RuntimeException('Belum ada user dengan role admin. Buat akun Admin dulu (DemoUserSeeder atau manual).');
        }
        $this->adminId = (int) $adminId;

        DB::transaction(function () {
            $this->categories();
            $this->rawMaterials();
            $this->productGroups();
            $this->products();
            $this->productMaterials();
        });
    }

    private function categories(): void
    {
        $added = 0;
        foreach ($this->readCsv('categories.csv') as $row) {
            if (DB::table('categories')->where('name', $row['name'])->exists()) {
                continue;
            }
            DB::table('categories')->insert(['name' => $row['name'], 'created_at' => now(), 'updated_at' => now()]);
            $added++;
        }
        $this->report('kategori', $added);
    }

    private function rawMaterials(): void
    {
        $added = 0;
        foreach ($this->readCsv('raw_materials.csv') as $row) {
            if (DB::table('raw_materials')->where('code', $row['code'])->exists()) {
                continue;
            }

            $stock = (float) ($row['current_stock'] ?? 0);

            $id = DB::table('raw_materials')->insertGetId([
                'code' => $row['code'],
                'name' => $row['name'],
                'unit' => $row['unit'],
                'material_type' => $row['material_type'],
                'sheet_length_cm' => $row['sheet_length_cm'],
                'sheet_width_cm' => $row['sheet_width_cm'],
                'current_stock' => $stock,
                'safety_stock' => $row['safety_stock'] ?? 0,
                'target_stock' => $row['target_stock'] ?? 0,
                'is_active' => $row['is_active'] === 'true',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($stock > 0) {
                $this->openingMovement('raw_material_movements', 'raw_material_id', $id, $stock);
            }
            $added++;
        }
        $this->report('bahan baku', $added);
    }

    private function productGroups(): void
    {
        $categories = DB::table('categories')->pluck('id', 'name');

        $added = 0;
        foreach ($this->readCsv('product_groups.csv') as $row) {
            if (DB::table('product_groups')->where('code', $row['code'])->exists()) {
                continue;
            }

            $categoryId = $row['category'] !== null ? ($categories[$row['category']] ?? null) : null;
            if ($row['category'] !== null && $categoryId === null) {
                throw new RuntimeException("Kategori {$row['category']} tidak ditemukan untuk induk {$row['code']}.");
            }

            DB::table('product_groups')->insert([
                'code' => $row['code'],
                'name' => $row['name'],
                'category_id' => $categoryId,
                'production_method' => $row['production_method'],
                'price' => $row['price'] ?? 0,
                'length_cm' => $row['length_cm'],
                'width_cm' => $row['width_cm'],
                'description' => $row['description'],
                'is_active' => $row['is_active'] === 'true',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $added++;
        }
        $this->report('produk induk', $added);
    }

    private function products(): void
    {
        $groups = DB::table('product_groups')->pluck('id', 'code');

        $added = 0;
        foreach ($this->readCsv('products.csv') as $row) {
            if (DB::table('products')->where('sku', $row['sku'])->exists()) {
                continue;
            }

            $groupId = $groups[$row['group_code']] ?? null;
            if ($groupId === null) {
                throw new RuntimeException("Induk {$row['group_code']} tidak ditemukan untuk SKU {$row['sku']}.");
            }

            $stock = (int) ($row['current_stock'] ?? 0);

            $id = DB::table('products')->insertGetId([
                'group_id' => $groupId,
                'sku' => $row['sku'],
                'variant_name' => $row['variant_name'],
                'current_stock' => $stock,
                'min_stock' => $row['min_stock'],
                'target_stock' => $row['target_stock'],
                'is_active' => $row['is_active'] === 'true',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($stock > 0) {
                $this->openingMovement('product_stock_movements', 'product_id', $id, $stock);
            }
            $added++;
        }
        $this->report('variasi (SKU)', $added);
    }

    private function productMaterials(): void
    {
        $groups = DB::table('product_groups')->pluck('id', 'code');
        $materials = DB::table('raw_materials')->get()->keyBy('code');

        $added = 0;
        foreach ($this->readCsv('product_materials.csv') as $row) {
            $groupId = $groups[$row['group_code']] ?? null;
            $material = $materials[$row['material_code']] ?? null;
            if ($groupId === null || $material === null) {
                throw new RuntimeException("Resep tidak valid: induk {$row['group_code']} / bahan {$row['material_code']} tidak ditemukan.");
            }

            if (DB::table('product_materials')->where('group_id', $groupId)->where('raw_material_id', $material->id)->exists()) {
                continue;
            }

            $waste = (float) ($row['waste_percent'] ?? 0);
            $pieceCount = (int) ($row['piece_count'] ?? 1);

            if ($row['usage_method'] === 'AREA') {
                if ($material->material_type !== 'SHEET' || ! $material->sheet_length_cm || ! $material->sheet_width_cm) {
                    throw new RuntimeException("Bahan {$material->code} bukan lembaran bertanda ukuran, tidak bisa memakai metode AREA.");
                }
                if ($row['piece_length_cm'] === null || $row['piece_width_cm'] === null) {
                    throw new RuntimeException("Ukuran potong wajib diisi untuk resep AREA {$row['group_code']} / {$row['material_code']}.");
                }

                // qty_per_unit = (jumlah potongan x panjang x lebar potong x (1 + limbah)) / (panjang x lebar lembar)
                $qty = round(
                    ($pieceCount * (float) $row['piece_length_cm'] * (float) $row['piece_width_cm'] * (1 + $waste / 100))
                    / ((float) $material->sheet_length_cm * (float) $material->sheet_width_cm),
                    6
                );
            } else {
                if ($row['qty_per_unit'] === null) {
                    throw new RuntimeException("qty_per_unit wajib diisi untuk resep FIXED {$row['group_code']} / {$row['material_code']}.");
                }
                $qty = (float) $row['qty_per_unit'];
            }

            DB::table('product_materials')->insert([
                'group_id' => $groupId,
                'raw_material_id' => $material->id,
                'usage_method' => $row['usage_method'],
                'piece_length_cm' => $row['piece_length_cm'],
                'piece_width_cm' => $row['piece_width_cm'],
                'piece_count' => $pieceCount,
                'waste_percent' => $waste,
                'qty_per_unit' => $qty,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $added++;
        }
        $this->report('resep', $added);
    }

    /** Stok awal dicatat sebagai movement ADJUSTMENT agar riwayat stok sesuai dengan stok sekarang. */
    private function openingMovement(string $table, string $foreignKey, int $id, int|float $qty): void
    {
        DB::table($table)->insert([
            $foreignKey => $id,
            'created_by' => $this->adminId,
            'movement_type' => 'ADJUSTMENT',
            'qty' => $qty,
            'qty_before' => 0,
            'qty_after' => $qty,
            'reference_type' => 'opening_stock',
            'reference_id' => null,
            'note' => 'Stok awal',
            'created_at' => now(),
        ]);
    }

    private function report(string $label, int $added): void
    {
        $this->command?->info("{$label}: {$added} baris baru");
    }

    /** @return array<int, array<string, string|null>> */
    private function readCsv(string $file): array
    {
        $path = database_path('seeders/data/' . $file);
        if (! is_file($path)) {
            throw new RuntimeException("File tidak ditemukan: {$path}");
        }

        $handle = fopen($path, 'r');
        $header = fgetcsv($handle, null, ',', '"', '\\');
        $rows = [];
        while (($line = fgetcsv($handle, null, ',', '"', '\\')) !== false) {
            if ($line === [null] || $line === []) {
                continue;
            }
            $row = array_combine($header, $line);
            // rapikan spasi ganda/di tepi (sering muncul saat salin dari Excel) agar pencocokan nama tidak gagal
            $rows[] = array_map(function ($value) {
                $value = trim((string) preg_replace('/\s+/u', ' ', (string) $value));

                return $value === '' ? null : $value;
            }, $row);
        }
        fclose($handle);

        return $rows;
    }
}
