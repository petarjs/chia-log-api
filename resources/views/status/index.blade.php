<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="pb-12 pt-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @foreach($machines as $machine)
            <div class="mb-12">
                <h2 class="text-4xl font-bold">{{$machine}}</h2>
                <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-3">
                    <a href="/status/{{$machine}}/disks">
                        <div class="px-4 py-5 bg-white shadow rounded-lg overflow-hidden sm:p-6">
                            <dd class="text-3xl font-semibold text-gray-900">
                                Disks
                            </dd>
                            <dt class="mt-1 text-sm font-medium text-gray-500 truncate">
                                Check mounted disks and their availability.
                            </dt>
                        </div>
                    </a>
                    
                    <a href="/status/{{$machine}}/sensors">
                        <div class="px-4 py-5 bg-white shadow rounded-lg overflow-hidden sm:p-6">
                            <dd class="text-3xl font-semibold text-gray-900">
                                Sensors
                            </dd>
                            <dt class="mt-1 text-sm font-medium text-gray-500 truncate">
                                Check machine sensor temperature.
                            </dt>
                        </div>
                    </a>
                    
                    <a href="/status/{{$machine}}/farm">
                        <div class="px-4 py-5 bg-white shadow rounded-lg overflow-hidden sm:p-6">
                            <dd class="text-3xl font-semibold text-gray-900">
                                Farm
                            </dd>
                            <dt class="mt-1 text-sm font-medium text-gray-500 truncate">
                                Check Chia farm status.
                            </dt>
                        </div>
                    </a>
                </dl>
            </div>
            @endforeach
        </div>
    </div>
</x-app-layout>