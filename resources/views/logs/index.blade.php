<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Logs') }} {{$machine}}
        </h2>
        <a href="{{ route('logs', 'chia-1') }}">chia-1</a>
        <a href="{{ route('logs', 'chia-2') }}">chia-2</a>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="js-log-container p-6 bg-black border-b border-gray-200 font-mono text-white" style="max-height: 580px; overflow: scroll">
                    <table>
                        @foreach($logLines as $log)
                            <tr>
                                <td style="vertical-align: top; min-width: 200px">{{$log->created_at}}</td>
                                <td class="text-green-600">{{$log->line}}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const $logContainer = document.querySelector('.js-log-container');
        $logContainer.scrollTop = $logContainer.scrollHeight;
    })
</script>
