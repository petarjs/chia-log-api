<?php

namespace App\Console\Commands;

use App\Models\LogLine;
use App\Models\Status;
use App\Notifications\SystemSummary;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;

class SendSummary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system-summary';

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
        $plots = LogLine::where('line', 'LIKE', '%Total plot creation time was%')->get();
        $totalTimes = $plots->map(function($line) {
            if (preg_match('/Total plot creation time was (.*) sec/m', $line, $match)) {
                $time = $match[1];
                return floatval($time);
            }
        });
        $avgTime = collect($totalTimes)->average();

        $status = Status::latest()->first();
        $farm = $status->farm;
        $walletInfo = $status->wallet;
        preg_match('/Plot count for all harvesters: (.*)\n/', $farm, $matches);
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

        $data = [
            'avgTotalTime' => number_format($avgTime, 0),
            'avgTotalTimeMin' => number_format($avgTime / 60, 2),
            'plotCount' => $plotCount,
            'plotSize' => number_format($plotSize, 2),
            'walletBalance' => number_format($walletBalance, 2),
            'walletBalanceUsd' => number_format($walletBalance * $xchPrice, 2),
            'xchPrice' => number_format($xchPrice, 2),
            'chia1Sensors' => $chia1Sensors,
        ];

        Notification::route('telegram', config('services.telegram-bot-api.chatId'))
            ->notify(new SystemSummary($data));
    }

    private function parseSensors($sensors) {
        preg_match('/radeon-pci-(.*)\nAdapter: PCI adapter\ntemp1:\s+(.*)°C\s/', $sensors, $matches);
        $cpu = $matches[2];
        preg_match('/nvme-pci-(.*)\nAdapter: PCI adapter\nComposite:\s+(.*)°C\s/', $sensors, $matches);
        $nvme = $matches[2];
        return compact('cpu', 'nvme');
    }
}
