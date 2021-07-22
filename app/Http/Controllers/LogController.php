<?php

namespace App\Http\Controllers;

use App\Http\Requests\LogRequest;
use App\Models\ApiKey;
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
        $data = $request->all();
        $key = $request->header('X-Authorization');
        $apiKey = ApiKey::where('key', $key)->first();

        if (!$apiKey) {
            abort(401);
        }

        $machine = $apiKey->name;
        $data['machine'] = $machine;
        $log = LogLine::create($data);

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
    
    public function index($machine = 'chia-1') {
        $logLines = LogLine::where('machine', $machine)->latest()->take(100)->get()->reverse();

        return view('logs.index', compact('logLines', 'machine'));
    }

    public function dash($machine = 'chia-1') {
        if (!$machine) {
            $machine = 'chia-1';
        }

        $plots = LogLine::where('machine', $machine)->where('line', 'LIKE', '%Total plot creation time was%')->get();
        $totalTimes = $plots->map(function($line) {
            if (preg_match('/Total plot creation time was (.*) sec/m', $line, $match)) {
                $time = $match[1];
                return floatval($time);
            }
        });
        $avgTime = collect($totalTimes)->average();
        $minTime = collect($totalTimes)->min();
        $maxTime = collect($totalTimes)->max();

        $plotCounts = LogLine::where('machine', $machine)->where('line', 'like', '%Total plot creation time was%')
            ->groupBy('date')
            ->orderBy('date', 'DESC')
            ->get([
                DB::raw('Date(created_at) as date'),
                DB::raw('COUNT(*) as "numPlots"')
            ]);

        foreach ($plotCounts as $plotCount) {
            $date = $plotCount['date'];
            $plotsDate = LogLine::where('machine', $machine)->where(DB::raw('Date(created_at)'), $date)
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

            $plotsDateCopy = LogLine::where('machine', $machine)->where(DB::raw('Date(created_at)'), $date)
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

        $status = Status::where('machine', $machine)->latest()->first();
        $farm = $status->farm;
        $walletInfo = $status->wallet;
        preg_match('/Plot count for all harvesters: (.*)\n/', $farm, $matches);
        $plotCount = $matches[1];
        preg_match('/of size: (.*) TiB/', $farm, $matches);
        try {
            $plotSize = $matches[1];
        } catch (\Throwable $th) {
            $plotSize = 0;
        }

        preg_match('/-Total Balance: (.*) xch/', $walletInfo, $matches);
        try {
            $walletBalance = $matches[1];
        } catch (\Throwable $th) {
            $walletBalance = 0;
        }

        $chia1SensorsText = Status::where('machine', $machine)->latest()->first()->sensors;
        $chia1Sensors = $this->parseSensors($chia1SensorsText);

        $xchPrice = Cache::remember('xchPrice', 1 * 60 * 60, function() {
            $cmc = new \CoinMarketCap\Api('7d313990-4234-4964-8dfa-94c04b15ebcd');
            $response = $cmc->cryptocurrency()->quotesLatest(['symbol' => 'XCH', 'convert' => 'USD']);
            $chiaPrice = $response->data->XCH->quote->USD->price;
            return $chiaPrice;
        });

        $diskInfo = $status->df;
        preg_match('/\s(\d+)T\s+(\d+)%\s\/mnt\/(sg|wd)(.*)\s/m', $diskInfo, $matches);
        try {
            $diskSize = $matches[1];
            $diskFilled = $matches[2];
            $diskName = $matches[3] . $matches[4];
            $disk = compact('diskSize', 'diskFilled', 'diskName');
        } catch (\Throwable $th) {
            $disk = [
                'diskName' => '',
                'diskSize' => '',
                'diskFilled' => '',
            ];
        }

        dd([$diskInfo, $disk, $matches]);

        return view('dashboard', [
            'machine' => $machine,
            'avgTotalTime' => number_format($avgTime, 0),
            'avgTotalTimeMin' => number_format($avgTime / 60, 2),
            'minTotalTimeMin' => number_format($minTime / 60, 2),
            'maxTotalTimeMin' => number_format($maxTime / 60, 2),
            'plotCounts' => $plotCounts,
            'plotCount' => $plotCount,
            'plotSize' => number_format($plotSize, 2),
            'walletBalance' => number_format($walletBalance, 2),
            'walletBalanceUsd' => number_format($walletBalance * $xchPrice, 2),
            'xchPrice' => number_format($xchPrice, 2),
            'chia1Sensors' => $chia1Sensors,
            'disk' => $disk,
        ]);
    }

    private function parseSensors($sensors) {
        try {
            // chia 1
            preg_match('/radeon-pci-(.*)\nAdapter: PCI adapter\ntemp1:\s+(.*)°C\s/', $sensors, $matches);
            $cpu = $matches[2];
            preg_match('/nvme-pci-(.*)\nAdapter: PCI adapter\nComposite:\s+(.*)°C\s/', $sensors, $matches);
            $nvme = $matches[2];
            return compact('cpu', 'nvme');
        } catch (\Throwable $th) {
            // chia 2
            preg_match('/Adapter: nvkm-0000:04:00\.0-bus-0002(.*)temp1:\s+(.*)°C\s+\((.*)Board Temp/s', $sensors, $matches);
            $cpu = $matches[2];
            preg_match('/nvme-pci-(.*)\nAdapter: PCI adapter\nComposite:\s+(.*)°C\s/', $sensors, $matches);
            $nvme = $matches[2];
            return compact('cpu', 'nvme');
        }
    }
}
