<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Enter Access Code for {{ $course->title }}
        </h2>
    </x-slot>

    <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-md mt-6">
        @if ($errors->any())
            <div class="mb-4 text-red-600">
                {{ $errors->first('access_code') }}
            </div>
        @endif

        <form action="{{ route('course.access-code.verify', $course->course_id) }}" method="POST">
            @csrf
            <div class="mb-4">
                <x-input-label for="access_code" :value="__('Access Code')" />
                <x-text-input id="access_code" class="block mt-1 w-full" type="text" name="access_code" required autofocus />
            </div>

            <x-primary-button class="mt-4">
                {{ __('Verify') }}
            </x-primary-button>
        </form>
    </div>
</x-app-layout>
