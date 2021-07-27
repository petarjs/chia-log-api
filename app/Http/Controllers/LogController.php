<?php

namespace App\Http\Controllers;

use App\Http\Requests\LogRequest;
use App\Models\ApiKey;
use App\Models\LogLine;
use App\Models\Plot;
use App\Models\Status;
use App\Models\User;
use App\Notifications\DiskOutOfSpace;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LogController extends Controller
{
    private $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function store(LogRequest $request)
    {
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
            foreach ($users as $user) {
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

    public function index($machine = 'chia-1')
    {
        $logLines = LogLine::where('machine', $machine)->latest()->take(100)->get()->reverse();

        return view('logs.index', compact('logLines', 'machine'));
    }

    public function dashTotals()
    {
        $general = $this->getGeneralDashDataFormatted();
        $c1 = $this->getAllDashDataFormatted('chia-1');
        $c2 = $this->getAllDashDataFormatted('chia-2');

        return view('dashboard-totals', compact('general', 'c1', 'c2'));
    }

    public function dash($machine = 'chia-1')
    {
        if (!$machine) {
            $machine = 'chia-1';
        }

        $plotCounts = LogLine::where('machine', $machine)->where('line', 'like', '%Total plot creation time was%')
            ->groupBy('date')
            ->orderBy('date', 'DESC')
            ->get([
                DB::raw('Date(created_at) as date'),
                DB::raw('COUNT(*) as "numPlots"')
            ]);
        // $plotCounts = Status
        //     ::select(
        //         DB::raw('Date(created_at) as date'),
        //         DB::raw("MAX(REPLACE(REGEXP_SUBSTR(statuses.farm, 'Plot count for all harvesters: (\\\\d+)'), 'Plot count for all harvesters: ', '')) as numPlots")
        //     )
        //     ->where('machine', $machine)
        //     ->groupBy('date')
        //     ->orderBy('date', 'DESC')
        //     ->get([
        //         DB::raw('date'),
        //         DB::raw('numPlots')
        //     ]);

        foreach ($plotCounts as $plotCount) {
            $date = $plotCount['date'];
            $plotsDate = LogLine::where('machine', $machine)->where(DB::raw('Date(created_at)'), $date)
                ->where('line', 'LIKE', '%Total plot creation time was%')
                ->get();
            $totalTimesDate = $plotsDate->map(function ($line) {
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
            $copyTimesDate = $plotsDateCopy->map(function ($plot) {
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
                collect($copyTimesDate)->average(function ($copyInfo) {
                    return $copyInfo['time'];
                }),
                2,
            );
            $plotCount['avgCopySpeed'] = number_format(
                collect($copyTimesDate)->average(function ($copyInfo) {
                    return $copyInfo['speed'];
                }),
                2,
            );
        }

        return view('dashboard', array_merge(
            [
                'machine' => $machine,
                'plotCounts' => $plotCounts,
            ],
            $this->getAllDashDataFormatted($machine),
            $this->getGeneralDashDataFormatted(),
        ));
    }

    private function getGeneralDashDataFormatted()
    {
        $generalData = $this->getGeneralDashData();

        return [
            'plotCountTotal' => $generalData['plotCountTotal'],
            'plotSizeTotal' => number_format($generalData['plotSizeTotal'], 2),
            'walletBalance' => number_format($generalData['walletBalance'], 2),
            'walletBalanceUsd' => number_format($generalData['walletBalance'] * $generalData['xchPrice'], 2),
            'xchPrice' => number_format($generalData['xchPrice'], 2),
        ];
    }

    private function getAllDashDataFormatted($machine)
    {
        $data = $this->getAllDashData($machine);

        return [
            'machine' => $machine,
            'avgTotalTime' => number_format($data['avgTime'], 0),
            'avgTotalTimeMin' => number_format($data['avgTime'] / 60, 2),
            'minTotalTimeMin' => number_format($data['minTime'] / 60, 2),
            'maxTotalTimeMin' => number_format($data['maxTime'] / 60, 2),
            'plotCount' => $data['plotCount'],
            'plotSize' => number_format($data['plotSize'], 2),
            'chia1Sensors' => $data['chia1Sensors'],
            'disk' => $data['disk'],
        ];
    }

    private function getGeneralDashData()
    {
        $plotCountC1 = $this->dashboardService->getFarmPlotCount('chia-1');
        $plotCountC2 = $this->dashboardService->getFarmPlotCount('chia-2');

        $plotCountTotal = $plotCountC1 + $plotCountC2;

        $plotSizeC1 = $this->dashboardService->getFarmPlotSize('chia-1');
        $plotSizeC2 = $this->dashboardService->getFarmPlotSize('chia-2');

        $plotSizeTotal = $plotSizeC1 + $plotSizeC2;

        $walletBalance = $this->dashboardService->getWalletBalance();

        $xchPrice = Cache::remember('xchPrice', 1 * 60 * 60, function () {
            return $this->dashboardService->getChiaPrice();
        });

        return compact('plotCountTotal', 'plotSizeTotal', 'walletBalance', 'xchPrice');
    }

    private function getAllDashData($machine)
    {
        $times = $this->dashboardService->getPlotTimes($machine);
        $avgTime = $times['avgTime'];
        $minTime = $times['minTime'];
        $maxTime = $times['maxTime'];

        $plotCount = $this->dashboardService->getFarmPlotCount($machine);

        $plotSize = $this->dashboardService->getFarmPlotSize($machine);

        $chia1Sensors = $this->dashboardService->parseSensors($machine);

        $disk = $this->dashboardService->getDiskInfo($machine);

        return compact(
            'avgTime',
            'maxTime',
            'minTime',
            'plotCount',
            'plotSize',
            'chia1Sensors',
            'disk',
        );
    }
}
