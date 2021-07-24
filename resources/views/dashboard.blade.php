<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
        <a href="{{ route('dashboard', 'chia-1') }}">chia-1</a>
        <a href="{{ route('dashboard', 'chia-2') }}">chia-2</a>
    </x-slot>

    <div class="pb-12 pt-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <dl class="mt-2 mb-5 grid grid-cols-1 gap-5 sm:grid-cols-4">
                    <div class="px-4 py-5 bg-white shadow-lg rounded-lg overflow-hidden sm:p-6 border-2 border-indigo-400">
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Total Plot Count
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                            {{ $plotCountTotal }}
                        </dd>
                    </div>

                    <div class="px-4 py-5 bg-white shadow-lg rounded-lg overflow-hidden sm:p-6 border-2 border-indigo-400">
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Total Plot Size
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                            {{ $plotSizeTotal }} TiB
                        </dd>
                    </div>

                    <div class="px-4 py-5 bg-white shadow-lg rounded-lg overflow-hidden sm:p-6 border-2 border-green-400">
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Wallet Balance
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                            ${{ $walletBalanceUsd }} ({{$walletBalance}} xch)
                        </dd>
                    </div>

                    <div class="px-4 py-5 bg-white shadow-lg rounded-lg overflow-hidden sm:p-6 border-2 border-green-400">
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            XCH price
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                            ${{ $xchPrice }}
                        </dd>
                    </div>
                </dl>

                <h1>
                    <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xl font-semibold bg-indigo-100 text-indigo-800">
                        {{$machine}}
                    </span>
                </h1>
                <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-4">
                    <div class="px-4 py-5 bg-white shadow-lg rounded-lg overflow-hidden sm:p-6 border-2 border-yellow-400">
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Avg. plot creation time
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                            {{$avgTotalTime}}s
                        </dd>
                    </div>

                    <div class="px-4 py-5 bg-white shadow-lg rounded-lg overflow-hidden sm:p-6 border-2 border-yellow-400">
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Avg. plot creation time
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                            {{$avgTotalTimeMin}} min
                        </dd>
                    </div>

                    <div class="px-4 py-5 bg-white shadow-lg rounded-lg overflow-hidden sm:p-6 border-2 border-yellow-400">
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Min. plot creation time
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                            {{$minTotalTimeMin}} min
                        </dd>
                    </div>

                    <div class="px-4 py-5 bg-white shadow-lg rounded-lg overflow-hidden sm:p-6 border-2 border-yellow-400">
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Max. plot creation time
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                            {{$maxTotalTimeMin}} min
                        </dd>
                    </div>

                    <div class="px-4 py-5 bg-white shadow-lg rounded-lg overflow-hidden sm:p-6 border-2 border-indigo-600">
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Plot count
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                            {{$plotCount}} plots
                        </dd>
                    </div>

                    <div class="px-4 py-5 bg-white shadow-lg rounded-lg overflow-hidden sm:p-6 border-2 border-indigo-600">
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Plot size
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                            {{$plotSize}} TiB
                        </dd>
                    </div>

                    <div class="px-4 py-5 bg-white shadow-lg rounded-lg overflow-hidden sm:p-6 border-2 border-pink-400">
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            CPU Temp Now
                            <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">{{$machine}}</span>
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                            {{$chia1Sensors['cpu']}}°C
                        </dd>
                    </div>

                    <div class="px-4 py-5 bg-white shadow-lg rounded-lg overflow-hidden sm:p-6 border-2 border-pink-400">
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            NVME Temp Now
                            <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">{{$machine}}</span>
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                            {{$chia1Sensors['nvme']}}°C
                        </dd>
                    </div>

                    <div class="px-4 py-5 bg-white shadow-lg rounded-lg overflow-hidden sm:p-6 border-2 border-blue-400">
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Disk Usage Now ({{$disk['name']}})
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                            {{$disk['filled']}}% (of {{$disk['size']}}TB)
                        </dd>
                    </div>
                </dl>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Number of plots
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Avg. plot time
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Avg. copy time and speed
                            </th>
                        </thead>
                        <tbody>
                            @foreach($plotCounts as $i => $plotCount)
                            <tr class="@if($i % 2 == 0) bg-white @else bg-gray-50 @endif">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{$plotCount->date}}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{$plotCount->numPlots}}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{$plotCount->avgTotalTimeMin}} min ({{$plotCount->avgTotalTime}}s)
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{$plotCount->avgCopyTime}}s ({{$plotCount->avgCopySpeed}} MB/s)
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>