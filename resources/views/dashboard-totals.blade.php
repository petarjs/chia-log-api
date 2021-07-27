<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Dashboard') }}
                </h2>
                <a href="{{ route('dashboard', 'chia-1') }}">chia-1</a>
                <a href="{{ route('dashboard', 'chia-2') }}">chia-2</a>
            </div>
            <div>
                <div class="px-2 py-1 bg-white shadow-lg rounded-lg overflow-hidden border border-gray-400">
                    <dd class="mt-1 text-xl font-semibold text-gray-900">
                        <span class="text-gray-500">XCH</span> ${{ $general['xchPrice'] }}
                    </dd>
                </div>
            </div>
        </div>
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
                            {{ $general['plotCountTotal'] }}
                        </dd>
                    </div>

                    <div class="px-4 py-5 bg-white shadow-lg rounded-lg overflow-hidden sm:p-6 border-2 border-indigo-400">
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Total Plot Size
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                            {{ $general['plotSizeTotal'] }} TiB
                        </dd>
                    </div>

                    <div class="px-4 py-5 bg-white shadow-lg rounded-lg overflow-hidden sm:p-6 border-2 border-green-400">
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Wallet Balance
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                            ${{ $general['walletBalanceUsd'] }}
                        </dd>
                    </div>

                    <div class="px-4 py-5 bg-white shadow-lg rounded-lg overflow-hidden sm:p-6 border-2 border-green-400">
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Wallet Balance (XCH)
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                            {{$general['walletBalance']}} xch
                        </dd>
                    </div>
                </dl>

                <h1>
                    <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xl font-semibold bg-indigo-100 text-indigo-800">
                        Chia 1
                    </span>
                </h1>
                <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-4">
                    <div class="px-4 py-5 bg-white shadow-lg rounded-lg overflow-hidden sm:p-6 border-2 border-yellow-400">
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Avg. plot creation time
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                            {{$c1['avgTotalTime']}}s
                        </dd>
                    </div>

                    <div class="px-4 py-5 bg-white shadow-lg rounded-lg overflow-hidden sm:p-6 border-2 border-yellow-400">
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Avg. plot creation time
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                            {{$c1['avgTotalTimeMin']}} min
                        </dd>
                    </div>

                    <div class="px-4 py-5 bg-white shadow-lg rounded-lg overflow-hidden sm:p-6 border-2 border-yellow-400">
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Min. plot creation time
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                            {{$c1['minTotalTimeMin']}} min
                        </dd>
                    </div>

                    <div class="px-4 py-5 bg-white shadow-lg rounded-lg overflow-hidden sm:p-6 border-2 border-yellow-400">
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Max. plot creation time
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                            {{$c1['maxTotalTimeMin']}} min
                        </dd>
                    </div>

                    <div class="px-4 py-5 bg-white shadow-lg rounded-lg overflow-hidden sm:p-6 border-2 border-indigo-600">
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Plot count
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                            {{$c1['plotCount']}} plots
                        </dd>
                    </div>

                    <div class="px-4 py-5 bg-white shadow-lg rounded-lg overflow-hidden sm:p-6 border-2 border-indigo-600">
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Plot size
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                            {{$c1['plotSize']}} TiB
                        </dd>
                    </div>

                    <div class="px-4 py-5 bg-white shadow-lg rounded-lg overflow-hidden sm:p-6 border-2 border-pink-400">
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            CPU Temp Now
                            <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">{{$c1['machine']}}</span>
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                            {{$c1['chia1Sensors']['cpu']}}°C
                        </dd>
                    </div>

                    <div class="px-4 py-5 bg-white shadow-lg rounded-lg overflow-hidden sm:p-6 border-2 border-pink-400">
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            NVME Temp Now
                            <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">{{$c1['machine']}}</span>
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                            {{$c1['chia1Sensors']['nvme']}}°C
                        </dd>
                    </div>

                    <div class="px-4 py-5 bg-white shadow-lg rounded-lg overflow-hidden sm:p-6 border-2 border-blue-400">
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Disk Usage Now ({{$c1['disk']['name']}})
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                            {{$c1['disk']['filled']}}% (of {{$c1['disk']['size']}}TB)
                        </dd>
                    </div>
                </dl>
            </div>

            <h1>
                <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xl font-semibold bg-indigo-100 text-indigo-800">
                    Chia 2
                </span>
            </h1>
            <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-4">
                <div class="px-4 py-5 bg-white shadow-lg rounded-lg overflow-hidden sm:p-6 border-2 border-yellow-400">
                    <dt class="text-sm font-medium text-gray-500 truncate">
                        Avg. plot creation time
                    </dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">
                        {{$c2['avgTotalTime']}}s
                    </dd>
                </div>

                <div class="px-4 py-5 bg-white shadow-lg rounded-lg overflow-hidden sm:p-6 border-2 border-yellow-400">
                    <dt class="text-sm font-medium text-gray-500 truncate">
                        Avg. plot creation time
                    </dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">
                        {{$c2['avgTotalTimeMin']}} min
                    </dd>
                </div>

                <div class="px-4 py-5 bg-white shadow-lg rounded-lg overflow-hidden sm:p-6 border-2 border-yellow-400">
                    <dt class="text-sm font-medium text-gray-500 truncate">
                        Min. plot creation time
                    </dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">
                        {{$c2['minTotalTimeMin']}} min
                    </dd>
                </div>

                <div class="px-4 py-5 bg-white shadow-lg rounded-lg overflow-hidden sm:p-6 border-2 border-yellow-400">
                    <dt class="text-sm font-medium text-gray-500 truncate">
                        Max. plot creation time
                    </dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">
                        {{$c2['maxTotalTimeMin']}} min
                    </dd>
                </div>

                <div class="px-4 py-5 bg-white shadow-lg rounded-lg overflow-hidden sm:p-6 border-2 border-indigo-600">
                    <dt class="text-sm font-medium text-gray-500 truncate">
                        Plot count
                    </dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">
                        {{$c2['plotCount']}} plots
                    </dd>
                </div>

                <div class="px-4 py-5 bg-white shadow-lg rounded-lg overflow-hidden sm:p-6 border-2 border-indigo-600">
                    <dt class="text-sm font-medium text-gray-500 truncate">
                        Plot size
                    </dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">
                        {{$c2['plotSize']}} TiB
                    </dd>
                </div>

                <div class="px-4 py-5 bg-white shadow-lg rounded-lg overflow-hidden sm:p-6 border-2 border-pink-400">
                    <dt class="text-sm font-medium text-gray-500 truncate">
                        CPU Temp Now
                        <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">{{$c2['machine']}}</span>
                    </dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">
                        {{$c2['chia1Sensors']['cpu']}}°C
                    </dd>
                </div>

                <div class="px-4 py-5 bg-white shadow-lg rounded-lg overflow-hidden sm:p-6 border-2 border-pink-400">
                    <dt class="text-sm font-medium text-gray-500 truncate">
                        NVME Temp Now
                        <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">{{$c2['machine']}}</span>
                    </dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">
                        {{$c2['chia1Sensors']['nvme']}}°C
                    </dd>
                </div>

                <div class="px-4 py-5 bg-white shadow-lg rounded-lg overflow-hidden sm:p-6 border-2 border-blue-400">
                    <dt class="text-sm font-medium text-gray-500 truncate">
                        Disk Usage Now ({{$c2['disk']['name']}})
                    </dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">
                        {{$c2['disk']['filled']}}% (of {{$c2['disk']['size']}}TB)
                    </dd>
                </div>
            </dl>
        </div>

    </div>
    </div>
</x-app-layout>