<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\MonthlyBill;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MonthlyBillController extends Controller
{
    public function index(Request $request)
    {
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);

        if ($month < 1 || $month > 12) {
            $month = now()->month;
        }

        if ($year < 2020 || $year > 2100) {
            $year = now()->year;
        }

        $search = trim($request->input('search', ''));
        $status = $request->input('status', '');

        if (!in_array($status, ['', 'UNPAID', 'PAID'])) {
            $status = '';
        }

        $bills = MonthlyBill::with('customer')
            ->where('billing_month', $month)
            ->where('billing_year', $year)
            ->when($search !== '', function ($query) use ($search) {
                $query->whereHas('customer', function ($customerQuery) use ($search) {
                    $customerQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('user_name', 'like', "%{$search}%");
                });
            })
            ->when($status !== '', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderBy('status')
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->withQueryString();

        $totalBills = MonthlyBill::where('billing_month', $month)
            ->where('billing_year', $year)
            ->count();

        $totalUnpaid = MonthlyBill::where('billing_month', $month)
            ->where('billing_year', $year)
            ->where('status', 'UNPAID')
            ->sum('amount');

        $totalPaid = MonthlyBill::where('billing_month', $month)
            ->where('billing_year', $year)
            ->where('status', 'PAID')
            ->sum('amount');

        return view('monthly-bills.index', compact(
            'bills',
            'month',
            'year',
            'totalBills',
            'totalUnpaid',
            'totalPaid',
            'search',
            'status'
        ));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2100',
        ]);

        $month = (int) $request->month;
        $year = (int) $request->year;

        $periodEnd = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();

        $customers = Customer::where('customer_status', 'AKTIF')
            ->whereNotNull('activation_date')
            ->whereDate('activation_date', '<=', $periodEnd)
            ->get();

        $created = 0;

        foreach ($customers as $customer) {
            $bill = MonthlyBill::firstOrCreate(
                [
                    'customer_id' => $customer->id,
                    'billing_year' => $year,
                    'billing_month' => $month,
                ],
                [
                    'amount' => $customer->billing_amount,
                    'status' => 'UNPAID',
                ]
            );

            if ($bill->wasRecentlyCreated) {
                $created++;
            }
        }

        return redirect()
            ->route('monthly-bills.index', [
                'month' => $month,
                'year' => $year,
            ])
            ->with('success', "Generate tagihan berhasil. {$created} tagihan baru dibuat.");
    }
}
