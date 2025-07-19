<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Stock;

class DashboardController extends Controller
{
    public function index()
    {
        $locations = Location::with(['stocks', 'transactions'])->get();

        $dashboardData = $locations->map(function ($location) {
            $total_gram_didapat = $location->stocks->sum('weight');
            $total_qty_kredit = $location->transactions->where('type', 'kredit')->sum('qty');
            $saldo_gram = $total_gram_didapat - $total_qty_kredit;

            $rekap_pengeluaran = $location->transactions->where('type', 'debit')->where('increase', '1')->sum('amount');
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

        $bulanIni = Carbon::now()->format('Y-m');

        $stocks = Stock::selectRaw('DATE(date) as tanggal, location_id, SUM(weight) as total')
            ->where('date', 'like', "$bulanIni%")
            ->groupBy('tanggal', 'location_id')
            ->orderBy('tanggal')
            ->get();

        $locations = Location::all()->keyBy('id');

        $tanggalList = $stocks->pluck('tanggal')->unique()->values();

        $locationList = $stocks->pluck('location_id')->unique()->values();

        $dataGrafik = [];

        foreach ($locationList as $locationId) {
            $data = [];

            foreach ($tanggalList as $tanggal) {
                $record = $stocks->firstWhere(fn($row) => $row->location_id == $locationId && $row->tanggal == $tanggal);
                $data[] = $record ? floatval($record->total) : 0;
            }

            $dataGrafik[] = [
                'label' => $locations[$locationId]->name,
                'data' => $data,
            ];
        }

        return view('dashboard', compact('dashboardData', 'tanggalList', 'dataGrafik'));
    }
}
