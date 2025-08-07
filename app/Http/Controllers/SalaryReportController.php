<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\Period;
use App\Models\Transaction;
use App\Models\Worker;
use App\Models\Absen;
use App\Models\Cashbon;
use App\Models\Stock;

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
        $location = Location::find($locationId);

        if (!$period || !$location) return [];

        // Get transaction data
        $totalKredit = Transaction::where('location_id', $locationId)
            ->where('period_id', $periodId)
            ->where('type', 'kredit')
            ->sum('amount');

        $totalDebit = Transaction::where('location_id', $locationId)
            ->where('period_id', $periodId)
            ->where('type', 'debit')
            ->where('increase', '1')
            ->sum('amount');

        $profit = $totalKredit - $totalDebit;

        $percentWorker = $location->percent_worker;
        $totalForWorkers = ($profit * $percentWorker) / 100;

        // Get stock weight data for the selected location and period
        $totalStockWeight = Stock::where('location_id', $locationId)
            ->whereBetween('date', [$period->date_start, $period->date_end])
            ->sum('weight');

        $workers = Worker::where('location_id', $locationId)->get();

        $absens = Absen::where('location_id', $locationId)
            ->whereBetween('date', [$period->date_start, $period->date_end])
            ->where('status', 'hadir')
            ->get()
            ->groupBy('worker_id');

        $totalHadir = $absens->flatten()->count();

        $result = $workers->map(function ($worker) use ($absens, $totalForWorkers, $totalHadir, $period, $locationId) {
            $jumlahHadir = isset($absens[$worker->id]) ? $absens[$worker->id]->count() : 0;
            $income = $totalHadir > 0 ? ($jumlahHadir / $totalHadir) * $totalForWorkers : 0;

            $cashbon = Cashbon::where('worker_id', $worker->id)
                ->where('location_id', $locationId)
                ->where('status', 'unpaid')
                ->whereBetween('date', [$period->date_start, $period->date_end])
                ->sum('amount');

            return [
                'name' => $worker->name,
                'jumlah_hadir' => $jumlahHadir,
                'penghasilan' => round($income, 2),
                'cashbon' => round($cashbon, 2),
                'terima' => round($income - $cashbon, 2),
            ];
        });

        return response()->json([
            'workers' => $result,
            'summary' => [
                'total_kredit' => $totalKredit,
                'total_debit' => $totalDebit,
                'profit' => $profit,
                'total_for_workers' => $totalForWorkers,
                'total_stock_weight' => $totalStockWeight,
                'location_name' => $location->name,
                'period_range' => $period->date_start . ' s/d ' . $period->date_end,
            ]
        ]);
    }
}
