<?php

namespace App\Http\Controllers\Bidder;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;

class TransactionController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // query dasar payments milik user
        $baseQuery = $user->payments();

        // statistik global (tidak terpengaruh pagination)
        $totalTrans   = (clone $baseQuery)->count();
        $paidCount    = (clone $baseQuery)->where('status', 'PAID')->count();
        $pendingCount = (clone $baseQuery)->where('status', 'PENDING')->count();
        $expiredCount = (clone $baseQuery)->where('status', 'EXPIRED')->count();

        // daftar transaksi 10 per halaman
        $payments = $baseQuery
            ->with(['lot.product.images'])
            ->orderByDesc('issued_at')
            ->paginate(10)
            ->withQueryString();

        return view('bidder.transactions.index', [
            'payments'      => $payments,
            'user'          => $user,
            'totalTrans'    => $totalTrans,
            'paidCount'     => $paidCount,
            'pendingCount'  => $pendingCount,
            'expiredCount'  => $expiredCount,
        ]);
    }

    public function show(Payment $payment)
    {
        $user = Auth::user();

        // pastikan payment ini memang milik user yang login
        if (! $payment->bidderProfile || $payment->bidderProfile->user_id !== $user->id) {
            abort(403);
        }

        $payment->load(['lot.product.images']);

        return view('bidder.transactions.show', [
            'payment' => $payment,
            'user'    => $user,
        ]);
    }
}
