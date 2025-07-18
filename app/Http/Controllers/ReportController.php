<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\Period;
use App\Models\Worker;
use App\Models\Absen;

class ReportController extends Controller
{
    public function index()
    {
        $locations = Location::orderBy('name')->get();
        return view('pages.report.absen', compact('locations'));
    }

    public function getPeriods($locationId)
    {
        $periods = Period::where('location_id', $locationId)
            ->orderByDesc('date_start')
            ->get();

        return response()->json($periods);
    }

    public function getReport(Request $request)
    {
        $request->validate([
            'location_id' => 'required|exists:locations,id',
            'period_id' => 'required|exists:periods,id',
        ]);

        $period = Period::find($request->period_id);

        $absens = Absen::where('location_id', $request->location_id)
        ->whereBetween('date', [$period->date_start, $period->date_end])
        ->with('worker')
        ->get()
        ->groupBy('worker_id');

        if ($absens->isEmpty()) {
            return response()->json([]);
        }

        $report = $absens->map(function ($records, $workerId) {
            $worker = $records->first()->worker;
            $hadir = $records->where('status', 'hadir')->pluck('date')->all();
            $tidakHadir = $records->where('status', 'tidak hadir')->pluck('date')->all();

            return [
                'name' => $worker->name,
                'hadir_dates' => $hadir,
                'tidak_hadir_dates' => $tidakHadir,
                'jumlah_hadir' => count($hadir),
            ];
        })->values();

        return response()->json($report);
    }
}
