<?php

namespace App\Console\Commands;

use App\Models\LogLine;
use App\Models\Plot;
use Illuminate\Console\Command;

class AddPlotIdToLogLines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'addPlotToLogLines';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $logLines = LogLine::all();
        collect($logLines)->each(function($log) {
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
            usleep(10);
        });
    }
}
