@props([
    'quotes' => [
        [
            'text' => 'Live as if you were to die tomorrow. Learn as if you were to live forever.',
            'author' => 'Mahatma Gandhi',
        ],
        [
            'text' => 'Education is the most powerful weapon which you can use to change the world.',
            'author' => 'Nelson Mandela',
        ],
        [
            'text' => 'The mind, once stretched by a new idea, never returns to its original dimensions.',
            'author' => 'Ralph Waldo Emerson',
        ],
        [
            'text' => 'Tell me and I forget. Teach me and I remember. Involve me and I learn.',
            'author' => 'Benjamin Franklin',
        ],
        [
            'text' => 'Develop a passion for learning. If you do, you will never cease to grow.',
            'author' => 'Anthony J. D’Angelo',
        ],
    ],
    'class' => '',
])


<div x-data="{ active: 0 }" x-init="setInterval(() => active = (active + 1) % {{ count($quotes) }}, 5000)"
    {{ $attributes->merge(['class' => $class . "px-16 bg-gradient-to-tr from-green-700 via-green-400 to-lime-300 flex justify-center flex-col gap-4 overflow-hidden relative"]) }}>

    @foreach ($quotes as $index => $quote)
        <div x-show="active === {{ $index }}" x-transition:enter="transition ease-out duration-700"
            x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-500" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-8"
            class="absolute inset-0 flex flex-col gap-4 px-16 mb-16 md:mb-0 justify-end md:justify-center">
            <h4 class="text-2xl font-extrabold text-white">"{{ $quote['text'] }}"</h4>
            <h5 class="text-lg font-light text-white">{{ $quote['author'] }}</h5>
        </div>
    @endforeach

</div>
