<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
    <form action="{{ route('course.index') }}" method="GET" class="flex gap-4 my-6">
        <input type="text" name="search" placeholder="Search by name, username or email"
            value="{{ request('search') }}" class="border px-3 py-2 rounded w-1/2">

        <select name="type" class="border pl-3 pr-12 py-2 rounded">
            <option value="public" {{ request('type') == 'public' ? 'selected' : '' }}>Public</option>
            <option value="private" {{ request('type') == 'private' ? 'selected' : '' }}>Private</option>
        </select>

        <button type="submit" class="!bg-red-600 text-white px-4 py-2 rounded">Filter</button>
    </form>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        @foreach ($courses as $course)
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <img src="{{ $course->cover_url }}" alt="{{ $course->title }}" class="w-full h-48 object-cover">

                <div class="p-4">
                    <h3 class="text-lg font-semibold mb-2">{{ $course->title }}</h3>
                    <p class="text-gray-600 text-sm mb-4">
                        {{ Str::limit($course->description, 100) }}
                    </p>
                    <a href="{{ $course->url }}"
                        class="inline-block bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded">
                        Lihat Detail
                    </a>
                </div>
            </div>
        @endforeach
    </div>

</x-app-layout>
