<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $data->machine }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    Sensors
                </div>

                <div class="p-6 bg-black border-b border-gray-200 font-mono text-green-600" style="max-height: 580px; overflow: scroll">
                    {{$data->sensors}}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
