<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\MonthlyBill;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function create(Customer $customer)
    {
        $bills = MonthlyBill::where('customer_id', $customer->id)
            ->where('status', 'UNPAID')
            ->orderBy('billing_year')
            ->orderBy('billing_month')
            ->get();

        return view('payments.create', compact('customer', 'bills'));
    }

    public function store(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'bill_ids' => 'required|array|min:1',
            'bill_ids.*' => 'required|integer',
            'paid_at' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ], [
            'bill_ids.required' => 'Pilih minimal satu tagihan.',
            'bill_ids.min' => 'Pilih minimal satu tagihan.',
            'paid_at.required' => 'Tanggal pembayaran wajib diisi.',
        ]);

        $payment = DB::transaction(function () use ($validated, $customer) {

            $billIds = collect($validated['bill_ids'])
                ->unique()
                ->values();

            $bills = MonthlyBill::where('customer_id', $customer->id)
                ->whereIn('id', $billIds)
                ->where('status', 'UNPAID')
                ->lockForUpdate()
                ->get();

            if ($bills->count() !== $billIds->count()) {
                throw ValidationException::withMessages([
                    'bill_ids' => 'Sebagian tagihan sudah dibayar atau tidak valid. Silakan refresh halaman.',
                ]);
            }

            $total = $bills->sum('amount');

            $payment = Payment::create([
                'customer_id' => $customer->id,
                'paid_at' => $validated['paid_at'],
                'total_amount' => $total,
                'notes' => $validated['notes'] ?? null,
            ]);

            $payment->monthlyBills()->attach($bills->pluck('id'));

            MonthlyBill::whereIn('id', $bills->pluck('id'))
                ->update([
                    'status' => 'PAID',
                    'paid_at' => $validated['paid_at'],
                ]);

            return $payment;
        });

        return redirect()
            ->route('monthly-bills.index')
            ->with('success', 'Pembayaran berhasil dicatat. Total: Rp ' .
                number_format((float) $payment->total_amount, 0, ',', '.'));
    }

    public function payBill(MonthlyBill $monthlyBill)
    {
        return DB::transaction(function () use ($monthlyBill) {

            $bill = MonthlyBill::whereKey($monthlyBill->id)
                ->lockForUpdate()
                ->firstOrFail();

            // Jika tagihan sudah LUNAS, batalkan pembayaran
            if ($bill->status === 'PAID') {

                $payment = $bill->payments()
                    ->lockForUpdate()
                    ->first();

                if (!$payment) {
                    throw ValidationException::withMessages([
                        'payment' => 'Transaksi pembayaran tidak ditemukan.',
                    ]);
                }

                // Lindungi transaksi yang mencakup beberapa tagihan
                if ($payment->monthlyBills()->count() > 1) {
                    throw ValidationException::withMessages([
                        'payment' => 'Pembayaran ini mencakup beberapa tagihan. Tidak dapat dibatalkan melalui tombol ini.',
                    ]);
                }

                // Hapus relasi pembayaran dengan tagihan
                $payment->monthlyBills()->detach($bill->id);

                // Hapus transaksi pembayaran
                $payment->delete();

                // Kembalikan status tagihan menjadi belum lunas
                $bill->update([
                    'status' => 'UNPAID',
                    'paid_at' => null,
                ]);

                return redirect()->back()->with(
                    'success',
                    'Pembayaran dibatalkan. Tagihan kembali belum lunas.'
                );
            }

            // Jika tagihan belum lunas, langsung catat pembayaran
            $paidAt = now();

            $payment = Payment::create([
                'customer_id' => $bill->customer_id,
                'paid_at' => $paidAt,
                'total_amount' => $bill->amount,
                'notes' => 'Pembayaran tagihan '
                    . $bill->billing_month . '/'
                    . $bill->billing_year,
            ]);

            $payment->monthlyBills()->attach($bill->id);

            $bill->update([
                'status' => 'PAID',
                'paid_at' => $paidAt,
            ]);

            return redirect()->back()->with(
                'success',
                'Tagihan berhasil dikonfirmasi lunas.'
            );
        });
    }
}
