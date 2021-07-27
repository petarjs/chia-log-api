<?php

namespace App\Services;

use App\Models\LogLine;
use App\Models\Status;

class DashboardService
{
    public function getPlotsForMachine($machine)
    {
        $plots = LogLine::where('machine', $machine)->where('line', 'LIKE', '%Total plot creation time was%')->get();
        return $plots;
    }
    public function getPlotTimes($machine)
    {
        $plots = $this->getPlotsForMachine($machine);
        $totalTimes = $plots->map(function ($line) {
            if (preg_match('/Total plot creation time was (.*) sec/m', $line, $match)) {
                $time = $match[1];
                return floatval($time);
            }
        });
        $avgTime = collect($totalTimes)->average();
        $minTime = collect($totalTimes)->min();
        $maxTime = collect($totalTimes)->max();

        return compact('totalTimes', 'avgTime', 'minTime', 'maxTime');
    }

    public function getFarmPlotCount($machine)
    {
        try {
            $status = Status::where('machine', $machine)->latest()->first();
            $farm = $status->farm;
            preg_match('/Plot count for all harvesters: (.*)\n/', $farm, $matches);
            $plotCount = $matches[1];
            return $plotCount;
        } catch (\Throwable $th) {
            return 0;
        }
    }

    public function getFarmPlotSize($machine)
    {
        try {
            $status = Status::where('machine', $machine)->latest()->first();
            preg_match('/of size: (.*) TiB/', $status->farm, $matches);
            $plotSizeTotal = floatval($matches[1]);
            return $plotSizeTotal;
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function getWalletBalance()
    {
        $status = Status::where('machine', 'chia-1')->latest()->first();
        preg_match('/-Total Balance: (.*) xch/', $status->wallet, $matches);
        try {
            $walletBalance = $matches[1];
            return $walletBalance;
        } catch (\Throwable $th) {
            return 0;
        }
    }

    public function parseSensors($machine)
    {
        try {
            $sensors = Status::where('machine', $machine)->latest()->first()->sensors;
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
        } catch (\Throwable $th) {
            return ['cpu' => 0, 'nvme' => 0];
        }
    }

    public function getChiaPrice()
    {
        $cmc = new \CoinMarketCap\Api('7d313990-4234-4964-8dfa-94c04b15ebcd');
        $response = $cmc->cryptocurrency()->quotesLatest(['symbol' => 'XCH', 'convert' => 'USD']);
        $chiaPrice = $response->data->XCH->quote->USD->price;
        return $chiaPrice;
    }

    public function getDiskInfo($machine)
    {
        try {
            $status = Status::where('machine', $machine)->latest()->first();
            $diskInfo = $status->df;
            preg_match_all('/\/dev\/(\w*)\s*([\w,]*)T\s+(.+)T\s+(.+)\s+(\d+)%\s+\/mnt\/(sg|wd)(.+)/', $diskInfo, $matches);
            $matchCount = count($matches[0]);

            for ($i = 0; $i < $matchCount; $i++) {
                $size = $matches[2][$i];
                $filled = $matches[5][$i];
                $name = $matches[6][$i] . $matches[7][$i];
                $disk = compact('size', 'filled', 'name');

                if ($filled > 0 && $filled < 100) {
                    return $disk;
                }
            }

            return [
                'size' => '',
                'filled' => '',
                'name' => '',
            ];
        } catch (\Throwable $th) {
            $disk = [
                'size' => '',
                'filled' => '',
                'name' => '',
            ];
            return $disk;
        }
    }
}
