<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\MonthlyBill;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

        $search = trim((string) $request->input('search', ''));
        $status = (string) $request->input('status', '');

        if (!in_array($status, ['', 'UNPAID', 'PAID'], true)) {
            $status = '';
        }

        $periodStart = Carbon::create($year, $month, 1)->startOfMonth();
        $periodEnd = $periodStart->copy()->endOfMonth();

        $escapedSearch = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search);

        $basePeriodQuery = MonthlyBill::query()
            ->where('billing_month', $month)
            ->where('billing_year', $year)
            ->whereHas('customer', function ($customerQuery) use ($periodStart) {
                $this->applyCustomerEligibilityScope($customerQuery, $periodStart);
            });

        $bills = (clone $basePeriodQuery)
            ->with(['customer.package'])
            ->when($escapedSearch !== '', function ($query) use ($escapedSearch) {
                $query->whereHas('customer', function ($customerQuery) use ($escapedSearch) {
                    $customerQuery->where(function ($subQuery) use ($escapedSearch) {
                        $subQuery->where('name', 'like', "%{$escapedSearch}%")
                            ->orWhere('user_name', 'like', "%{$escapedSearch}%")
                            ->orWhere('address', 'like', "%{$escapedSearch}%");
                    });
                });
            })
            ->when($status !== '', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderBy('id')
            ->paginate(100)
            ->withQueryString();

        $totalBills = (clone $basePeriodQuery)->count();
        $totalUnpaid = (clone $basePeriodQuery)->where('status', 'UNPAID')->sum('amount');
        $totalPaid = (clone $basePeriodQuery)->where('status', 'PAID')->sum('amount');

        $paymentsReceived = Payment::whereBetween('paid_at', [
            $periodStart->copy()->startOfDay(),
            $periodEnd->copy()->endOfDay(),
        ])->sum('total_amount');

        $totalArrears = MonthlyBill::where('status', 'UNPAID')
            ->whereHas('customer', function ($customerQuery) use ($periodEnd) {
                $customerQuery->whereRaw('UPPER(TRIM(customer_status)) = ?', ['AKTIF'])
                    ->where(function ($eligibilityQuery) use ($periodEnd) {
                        $eligibilityQuery
                            ->where(function ($q) use ($periodEnd) {
                                $q->whereNotNull('activation_date')
                                    ->whereDate('activation_date', '<=', $periodEnd->toDateString());
                            })
                            ->orWhere(function ($q) use ($periodEnd) {
                                $q->whereNull('activation_date')
                                    ->whereDate('created_at', '<=', $periodEnd->toDateString());
                            });
                    });
            })
            ->sum('amount');

        if ($request->ajax()) {
            return view('monthly-bills._ajax-results', compact(
                'bills',
                'month',
                'year',
                'search',
                'status'
            ));
        }

        return view('monthly-bills.index', compact(
            'bills',
            'month',
            'year',
            'totalBills',
            'totalUnpaid',
            'totalPaid',
            'paymentsReceived',
            'totalArrears',
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

        $periodStart = Carbon::create($year, $month, 1)->startOfMonth();

        // Hanya pelanggan AKTIF yang tanggal mulainya jatuh sebelum awal periode
        // (artinya pelanggan baru di bulan ini baru masuk ke periode berikutnya).
        $customers = Customer::with('package')
            ->where(function ($customerQuery) use ($periodStart) {
                $this->applyCustomerEligibilityScope($customerQuery, $periodStart);
            })
            ->get();

        if ($customers->isEmpty()) {
            return redirect()
                ->route('monthly-bills.index', [
                    'month' => $month,
                    'year' => $year,
                ])
                ->with(
                    'success',
                    "Generate tagihan selesai. 0 tagihan baru dibuat untuk periode {$periodStart->format('m/Y')} (tidak ada pelanggan yang memenuhi syarat untuk periode ini)."
                );
        }

        $existingCustomerIds = MonthlyBill::where('billing_year', $year)
            ->where('billing_month', $month)
            ->whereIn('customer_id', $customers->pluck('id'))
            ->pluck('customer_id')
            ->flip();

        $now = now();
        $rowsToInsert = [];

        foreach ($customers as $customer) {
            if ($existingCustomerIds->has($customer->id)) {
                continue;
            }

            $amount = $customer->package
                ? (float) $customer->package->price
                : (float) $customer->billing_amount;

            $rowsToInsert[] = [
                'customer_id' => $customer->id,
                'billing_year' => $year,
                'billing_month' => $month,
                'amount' => $amount,
                'status' => 'UNPAID',
                'paid_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($rowsToInsert)) {
            MonthlyBill::insert($rowsToInsert);
        }

        $created = count($rowsToInsert);

        return redirect()
            ->route('monthly-bills.index', [
                'month' => $month,
                'year' => $year,
            ])
            ->with(
                'success',
                "Generate tagihan berhasil. {$created} tagihan baru dibuat untuk periode {$periodStart->format('m/Y')}."
            );
    }

    /**
     * Pelanggan baru yang dibuat/diaktifkan pada bulan X baru mulai ditagih
     * pada awal bulan X + 1 (periode berikutnya).
     */
    private function applyCustomerEligibilityScope($query, Carbon $periodStart): void
    {
        $query->whereRaw('UPPER(TRIM(customer_status)) = ?', ['AKTIF'])
            ->where(function ($eligibilityQuery) use ($periodStart) {
                // Jika activation_date terisi: awal bulan berikutnya setelah activation_date <= periodStart
                $eligibilityQuery->where(function ($q) use ($periodStart) {
                    $q->whereNotNull('activation_date')
                        ->whereRaw(
                            "DATE_FORMAT(DATE_ADD(activation_date, INTERVAL 1 MONTH), '%Y-%m-01') <= ?",
                            [$periodStart->toDateString()]
                        );
                })
                    // Jika activation_date kosong: gunakan created_at (awal bulan berikutnya <= periodStart)
                    ->orWhere(function ($q) use ($periodStart) {
                        $q->whereNull('activation_date')
                            ->whereRaw(
                                "DATE_FORMAT(DATE_ADD(created_at, INTERVAL 1 MONTH), '%Y-%m-01') <= ?",
                                [$periodStart->toDateString()]
                            );
                    });
            });
    }
}
