@foreach (session('flash_notification', collect())->toArray() as $message)
    @if ($message['overlay'])
        @include('flash::modal', [
            'modalClass' => 'flash-modal',
            'title' => $message['title'],
            'body' => $message['message'],
        ])
    @else
        @php
            $baseClass = 'rounded px-4 py-3';
            $colors = [
                'success' => 'bg-green-100 text-green-800',
                'danger' => 'bg-red-100 text-red-800',
                'warning' => 'bg-yellow-100 text-yellow-800',
                'info' => 'bg-blue-100 text-blue-800',
            ];
        @endphp

        <div class="{{ $baseClass }} {{ $colors[$message['level']] ?? 'bg-gray-100 text-gray-800' }} 
            fixed left-1/2 top-10 transform -translate-x-1/2 z-50 shadow-lg w-fit max-w-xs"
            role="alert">
            <div class="flex justify-between items-center">
                <div>{!! $message['message'] !!}</div>
                @if ($message['important'])
                    <button onclick="this.parentElement.parentElement.remove()"
                        class="ml-4 text-sm text-gray-500 hover:text-gray-700">
                        &times;
                    </button>
                @endif
            </div>
        </div>
    @endif
@endforeach

{{ session()->forget('flash_notification') }}
