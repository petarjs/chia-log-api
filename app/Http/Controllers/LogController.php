<?php

namespace App\Http\Controllers;

use App\Http\Requests\LogRequest;
use App\Models\LogLine;
use App\Models\Plot;
use Illuminate\Http\Request;

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
}
