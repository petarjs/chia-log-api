<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    Average plot creation time: {{$avgTotalTime}} sec ({{$avgTotalTimeMin}} min)
                </div>
                <div class="p-6 bg-white border-b border-gray-200">
                    <table>
                        <thead>
                            <th>Date</th>
                            <th>Number of plots</th>
                        </thead>
                        <tbody>
                            @foreach($plotCounts as $plotCount)
                                <tr>
                                    <td>{{$plotCount->date}}</td>
                                    <td>{{$plotCount->num_plots}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
