<?php

namespace App\Imports;

use App\Models\Package;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PackagesImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $name = trim((string) ($row['jenis_paket'] ?? ''));

            if ($name === '') {
                continue;
            }

            $price = $row['harga_paket'] ?? 0;

            Package::updateOrCreate(
                ['name' => $name],
                ['price' => (float) $price]
            );
        }
    }
}
