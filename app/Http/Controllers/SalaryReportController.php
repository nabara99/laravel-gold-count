<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SalaryReportController extends Controller
{
    public function index()
    {
        $locations = Location::orderBy('name')->get();
        return view('pages.report.salary', compact('locations'));
    }

    public function getPeriods($locationId)
    {
        return Period::where('location_id', $locationId)->orderByDesc('date_start')->get();
    }

    public function getIncomeData(Request $request)
    {
        $locationId = $request->location_id;
        $periodId = $request->period_id;

        $period = Period::find($periodId);
        if (!$period) return [];

        // Hitung total kredit dan debit
        $totalKredit = Transaction::where('location_id', $locationId)
            ->where('period_id', $periodId)
            ->where('type', 'kredit')
            ->sum('amount');

        $totalDebit = Transaction::where('location_id', $locationId)
            ->where('period_id', $periodId)
            ->where('type', 'debit')
            ->sum('amount');

        $profit = $totalKredit - $totalDebit;
        $totalForWorkers = $profit * 0.3;

        // Ambil absensi semua pekerja dan jumlah hadir
        $workers = Worker::where('location_id', $locationId)->get();
        $absens = Absen::where('location_id', $locationId)
            ->whereBetween('date', [$period->date_start, $period->date_end])
            ->where('status', 'hadir')
            ->get()
            ->groupBy('worker_id');

        $totalHadir = $absens->flatten()->count();

        // Hitung penghasilan per pekerja
        $result = $workers->map(function ($worker) use ($absens, $totalForWorkers, $totalHadir) {
            $jumlahHadir = isset($absens[$worker->id]) ? $absens[$worker->id]->count() : 0;
            $income = $totalHadir > 0 ? ($jumlahHadir / $totalHadir) * $totalForWorkers : 0;

            return [
                'name' => $worker->name,
                'jumlah_hadir' => $jumlahHadir,
                'penghasilan' => round($income, 2),
            ];
        });

        return response()->json($result);
    }

}
