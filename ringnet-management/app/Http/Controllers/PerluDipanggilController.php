<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\MonthlyBill;
use Illuminate\Http\Request;

class PerluDipanggilController extends Controller
{
    public function index()
    {
        // Ambil periode tagihan terbaru yang tersedia
        $latestBill = MonthlyBill::orderByDesc('billing_year')
            ->orderByDesc('billing_month')
            ->first();

        $customers = collect();

        if ($latestBill) {
            $latestYear = (int) $latestBill->billing_year;
            $latestMonth = (int) $latestBill->billing_month;

            // Ambil semua pelanggan beserta tagihannya
            $allCustomers = Customer::with('monthlyBills')
                ->get();

            foreach ($allCustomers as $customer) {
                $consecutiveUnpaid = 0;
                $arrearsPeriods = [];
                $totalArrears = 0;

                $year = $latestYear;
                $month = $latestMonth;

                // Hitung tunggakan mundur dari periode terbaru
                while (true) {
                    $bill = $customer->monthlyBills
                        ->first(function ($bill) use ($year, $month) {
                            return (int) $bill->billing_year === $year
                                && (int) $bill->billing_month === $month;
                        });

                    // Jika tagihan periode ini tidak ada,
                    // hentikan perhitungan
                    if (!$bill) {
                        break;
                    }

                    // Jika sudah dibayar, hentikan hitungan beruntun
                    if ($bill->status !== 'UNPAID') {
                        break;
                    }

                    $consecutiveUnpaid++;

                    $arrearsPeriods[] = sprintf(
                        '%02d/%d',
                        $month,
                        $year
                    );

                    $totalArrears += (float) $bill->amount;

                    // Mundur satu bulan
                    $month--;

                    if ($month < 1) {
                        $month = 12;
                        $year--;
                    }
                }

                // Tampilkan pelanggan dengan tunggakan 2 bulan atau lebih
                if ($consecutiveUnpaid >= 2 && $consecutiveUnpaid <= 3) {
                    $customers->push([
                        'id' => $customer->id,
                        'name' => $customer->name,
                        'address' => $customer->address,
                        'phone' => $customer->phone,
                        'consecutive_unpaid' => $consecutiveUnpaid,
                        'arrears_periods' => $arrearsPeriods,
                        'total_arrears' => $totalArrears,
                    ]);
                }
            }
        }

        // Urutkan berdasarkan tunggakan terbanyak
        $customers = $customers->sortByDesc('consecutive_unpaid')
            ->values();

        return view('perlu-dipanggil.index', compact('customers'));
    }
}
