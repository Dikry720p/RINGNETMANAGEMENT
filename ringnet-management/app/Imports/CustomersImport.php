<?php

namespace App\Imports;

use App\Models\Customer;
use App\Models\Package;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomersImport implements ToCollection, WithHeadingRow
{
    public function headingRow(): int
    {
        // Header Master Data berada di baris ke-2
        return 2;
    }

    public function collection(Collection $rows): void
    {
        // Index paket dengan nama yang sudah dinormalisasi
        $packages = Package::all()->keyBy(function ($package) {
            return $this->normalize($package->name);
        });

        foreach ($rows as $row) {
            $name = trim((string) ($row['nama_pelanggan'] ?? ''));
            $username = trim((string) ($row['nama_user'] ?? ''));

            // Lewati baris tanpa identitas pelanggan
            if ($name === '' && $username === '') {
                continue;
            }

            $packageName = trim((string) ($row['jenis_paket'] ?? ''));
            $package = $packages->get($this->normalize($packageName));

            // Paket tidak ditemukan, baris dilewati
            if (!$package) {
                continue;
            }

            $activationDate = $this->parseDate(
                $row['tgl_aktivasi'] ?? null
            );

            $billingAmount = $row['jumlah_tagihan'] ?? $package->price;

            $customerData = [
                'name'            => $name,
                'user_name'       => $username ?: null,
                'phone'           => $row['nomor_tlp'] ?? null,
                'address'         => $row['alamat'] ?? null,
                'activation_date' => $activationDate,
                'package_id'       => $package->id,
                'billing_amount'   => is_numeric($billingAmount)
                    ? $billingAmount
                    : $package->price,
                'sales'            => $row['sales'] ?? null,
                'customer_status'   => strtoupper(
                    trim((string) ($row['status_pelanggan'] ?? 'AKTIF'))
                ),
            ];

            // Identifikasi pelanggan untuk mencegah duplikat
            $query = Customer::query()
                ->where('name', $name)
                ->where('address', $customerData['address']);

            if ($username !== '') {
                $query->where('user_name', $username);
            } else {
                $query->whereNull('user_name')
                    ->where('activation_date', $activationDate)
                    ->where('package_id', $package->id);
            }

            $customer = $query->first();

            if ($customer) {
                $customer->update($customerData);
            } else {
                Customer::create($customerData);
            }
        }
    }

    private function normalize(string $value): string
    {
        return strtolower(
            preg_replace('/[^a-z0-9]/i', '', trim($value))
        );
    }

    private function parseDate($value): ?string
    {
        if (!$value) {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value)->format('Y-m-d');
        }

        $value = trim((string) $value);

        $months = [
            'januari'  => 'January',
            'februari' => 'February',
            'maret'    => 'March',
            'april'    => 'April',
            'mei'      => 'May',
            'juni'     => 'June',
            'juli'     => 'July',
            'agustus'  => 'August',
            'september' => 'September',
            'oktober'  => 'October',
            'november' => 'November',
            'desember' => 'December',
        ];

        foreach ($months as $indo => $english) {
            $value = str_ireplace($indo, $english, $value);
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }
}
