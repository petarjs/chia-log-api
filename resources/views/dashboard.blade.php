<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="pb-12 pt-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-3">
                    <div class="px-4 py-5 bg-white shadow rounded-lg overflow-hidden sm:p-6">
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Avg. plot creation time (s)
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                            {{$avgTotalTime}}s
                        </dd>
                    </div>

                    <div class="px-4 py-5 bg-white shadow rounded-lg overflow-hidden sm:p-6">
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Avg. plot creation time (min)
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                            {{$avgTotalTimeMin}} min
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