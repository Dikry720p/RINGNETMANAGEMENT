<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\MonthlyBill;

class BermasalahController extends Controller
{
    public function index()
    {
        $latestBill = MonthlyBill::orderByDesc('billing_year')
            ->orderByDesc('billing_month')
            ->first();

        $customers = collect();

        if ($latestBill) {
            $latestYear = (int) $latestBill->billing_year;
            $latestMonth = (int) $latestBill->billing_month;

            $allCustomers = Customer::with('monthlyBills')->get();

            foreach ($allCustomers as $customer) {
                $consecutiveUnpaid = 0;
                $arrearsPeriods = [];
                $totalArrears = 0;

                $year = $latestYear;
                $month = $latestMonth;

                // Hitung tunggakan berturut-turut dari periode terbaru
                while (true) {
                    $bill = $customer->monthlyBills
                        ->first(function ($bill) use ($year, $month) {
                            return (int) $bill->billing_year === $year
                                && (int) $bill->billing_month === $month;
                        });

                    if (!$bill || $bill->status !== 'UNPAID') {
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

                // Bermasalah: tunggakan 4 bulan berturut-turut atau lebih
                if ($consecutiveUnpaid >= 4) {
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

        // Urutkan dari tunggakan terbanyak
        $customers = $customers->sortByDesc('consecutive_unpaid')
            ->values();

        return view('bermasalah.index', compact('customers'));
    }
}
