<?php

namespace App\Http\Controllers;

use App\Models\Location;

class DashboardController extends Controller
{
    public function index()
    {
        $locations = Location::with(['stocks', 'transactions'])->get();

        $dashboardData = $locations->map(function ($location) {
            $total_gram_didapat = $location->stocks->sum('weight');
            $total_qty_kredit = $location->transactions->where('type', 'kredit')->sum('qty');
            $saldo_gram = $total_gram_didapat - $total_qty_kredit;

            $rekap_pengeluaran = $location->transactions->where('type', 'debit')->sum('amount');
            $rekap_pemasukan = $location->transactions->where('type', 'kredit')->sum('amount');
            $saldo_uang = $rekap_pemasukan - $rekap_pengeluaran;

            return [
                'location' => $location->name,
                'total_gram_didapat' => $total_gram_didapat,
                'saldo_gram' => $saldo_gram,
                'rekap_pengeluaran' => $rekap_pengeluaran,
                'saldo_uang' => $saldo_uang
            ];
        });

        return view('dashboard', compact('dashboardData'));
    }
}
