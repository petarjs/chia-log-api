<?php

namespace App\Http\Controllers;

use App\Http\Requests\LogRequest;
use App\Models\LogLine;
use App\Models\Plot;
use App\Models\Status;
use App\Models\User;
use App\Notifications\DiskOutOfSpace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LogController extends Controller
{
    public function store(LogRequest $request) {
        $log = LogLine::create($request->all());

        if (str_contains($log->line, 'fwrite() failed')) {
            $users = User::all();
            foreach($users as $user) {
                $user->notify(new DiskOutOfSpace());
            }
        }

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
        $logLines = LogLine::latest()->take(100)->get()->reverse();

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
                DB::raw('COUNT(*) as "numPlots"')
            ]);

        foreach ($plotCounts as $plotCount) {
            $date = $plotCount['date'];
            $plotsDate = LogLine::where(DB::raw('Date(created_at)'), $date)
                ->where('line', 'LIKE', '%Total plot creation time was%')
                ->get();
            $totalTimesDate = $plotsDate->map(function($line) {
                if (preg_match('/Total plot creation time was (.*) sec/m', $line, $match)) {
                    $time = $match[1];
                    return floatval($time);
                }
            });
            $avgTimeDate = collect($totalTimesDate)->average();
            $plotCount['avgTotalTime'] = number_format($avgTimeDate, 0);
            $plotCount['avgTotalTimeMin'] = number_format($avgTimeDate / 60, 2);

            $plotsDateCopy = LogLine::where(DB::raw('Date(created_at)'), $date)
                ->where('line', 'LIKE', '%Copy to%.plot finished%')
                ->get();
            $copyTimesDate = $plotsDateCopy->map(function($plot) {
                $line = $plot->line;
                if (preg_match('/plot finished, took (.*) sec, (.*) MB\/s avg/m', $line, $match)) {
                    $time = $match[1];
                    $speed = $match[2];
                    return [
                        'time' => floatval($time),
                        'speed' => floatval($speed),
                    ];
                }
            });
            $plotCount['avgCopyTime'] = number_format(
                collect($copyTimesDate)->average(function($copyInfo) {
                    return $copyInfo['time'];
                }),
                2,
            );
            $plotCount['avgCopySpeed'] = number_format(
                collect($copyTimesDate)->average(function($copyInfo) {
                    return $copyInfo['speed'];
                }),
                2,
            );
        }

        $status = Status::latest()->first();
        $farm = $status->farm;
        $walletInfo = $status->wallet;
        preg_match('/Plot count for all harvesters: (.*)\n/', $farm, $matches);
        dd($matches);
        $plotCount = $matches[1];
        preg_match('/of size: (.*) TiB/', $farm, $matches);
        $plotSize = $matches[1];

        preg_match('/-Total Balance: (.*) xch/', $walletInfo, $matches);
        try {
            $walletBalance = $matches[1];
        } catch (\Throwable $th) {
            $walletBalance = 0;
        }

        $chia1SensorsText = Status::latest()->where('machine', 'chia-1')->first()->sensors;
        $chia1Sensors = $this->parseSensors($chia1SensorsText);

        $xchPrice = Cache::remember('xchPrice', 1 * 60 * 60, function() {
            $cmc = new \CoinMarketCap\Api('7d313990-4234-4964-8dfa-94c04b15ebcd');
            $response = $cmc->cryptocurrency()->quotesLatest(['symbol' => 'XCH', 'convert' => 'USD']);
            $chiaPrice = $response->data->XCH->quote->USD->price;
            return $chiaPrice;
        });

        return view('dashboard', [
            'avgTotalTime' => number_format($avgTime, 0),
            'avgTotalTimeMin' => number_format($avgTime / 60, 2),
            'plotCounts' => $plotCounts,
            'plotCount' => $plotCount,
            'plotSize' => number_format($plotSize, 2),
            'walletBalance' => number_format($walletBalance, 2),
            'walletBalanceUsd' => number_format($walletBalance * $xchPrice, 2),
            'xchPrice' => number_format($xchPrice, 2),
            'chia1Sensors' => $chia1Sensors,
        ]);
    }

    private function parseSensors($sensors) {
        preg_match('/radeon-pci-(.*)\nAdapter: PCI adapter\ntemp1:\s+(.*)°C\s/', $sensors, $matches);
        $cpu = $matches[2];
        preg_match('/nvme-pci-(.*)\nAdapter: PCI adapter\nComposite:\s+(.*)°C\s/', $sensors, $matches);
        $nvme = $matches[2];
        return compact('cpu', 'nvme');
    }
}
