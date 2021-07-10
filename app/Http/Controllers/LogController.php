<?php

namespace App\Http\Controllers;

use App\Http\Requests\LogRequest;
use App\Models\LogLine;
use App\Models\Plot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogController extends Controller
{
    public function store(LogRequest $request) {
        $log = LogLine::create($request->all());

        if (str_contains($log->line, 'Plot Name:')) {
            // Started new plot

            if (preg_match('/Plot Name: (.*)/m', $log->line, $match)) {
                $newPlot = Plot::create([
                    'name' => $match[1],
                    'config' => '',
                    'copy_speed' => 0,
                    'copy_time' => 0,
                    'p1_time' => 0,
                    'total_time' => 0,
                    'complete' => false,
                ]);
                $log->plot_id = $newPlot->id;
            }

        } else if (str_contains($log->line, 'plot finished, took')) {
            // Plot copy finished, attach line to the previous plot
            $currentPlot = Plot::latest()->first();
            $previousPlot = Plot::where(['id' => $currentPlot->id - 1])->first();
            if ($previousPlot) {
                $log->plot_id = $previousPlot->id;
            }
        } else {
            // Attach line to the last plot
            $currentPlot = Plot::latest()->first();
            if ($currentPlot) {
                $log->plot_id = $currentPlot->id;
            }
        }
        $log->save();
    }
    
    public function index() {
        $logLines = LogLine::all();

        return view('logs.index', compact('logLines'));
    }
        
    public function dash() {
        $plots = LogLine::where('line', 'LIKE', '%Total plot creation time was%')->get();
        $totalTimes = $plots->map(function($line) {
            if (preg_match('/Total plot creation time was (.*) sec/m', $line, $match)) {
                $time = $match[1];
                return floatval($time);
            }
        });
        $avgTime = collect($totalTimes)->average();

        $plotCounts = LogLine::where('line', 'like', '%Total plot creation time was%')
                            ->groupBy('date')
                            ->orderBy('date', 'DESC')
                            ->get([
                                DB::raw('Date(created_at) as date'),
                                DB::raw('COUNT(*) as "num_plots"')
                            ]);

        return view('dashboard', [
            'avgTotalTime' => $avgTime,
            'avgTotalTimeMin' => $avgTime / 60,
            'plotCounts' => $plotCounts,
        ]);
    }
}
