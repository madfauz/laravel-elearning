@props(['id', 'message' => 'Are you sure?', 'okLabel' => 'OK', 'cancelLabel' => 'Cancel', 'url' => null, 'method', 'formId' => null])

<div x-data="{ open: false }"
     x-on:open-modal.window="if ($event.detail === '{{ $id }}') open = true"
     x-show="open"
     x-transition
     class="fixed inset-0 bg-black bg-opacity-50 z-50"
     style="display: none">

    <div class="bg-white rounded-lg shadow-lg max-w-sm mx-auto mt-40 p-6 text-center">
        <p class="mb-4 text-gray-800">{{ $message }}</p>

        <div class="flex justify-center gap-4">
            @if ($formId)
                <button type="submit" form="{{ $formId }}" class="bg-green-500 text-white px-4 py-2 rounded">
                    {{ $okLabel }}
                </button>
            @elseif ($url)
                <form method="POST" action="{{ $url }}">
                    @csrf
                    @if ($method)
                        @method($method)
                    @endif
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">
                        {{ $okLabel }}
                    </button>
                </form>
            @endif

            <button type="button" @click="open = false" class="bg-gray-300 text-gray-800 px-4 py-2 rounded">
                {{ $cancelLabel }}
            </button>
        </div>
    </div>
</div>
