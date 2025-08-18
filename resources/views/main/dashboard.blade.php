<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Find Available Course') }}
        </h2>
    </x-slot>

    <form action="{{ route('main.dashboard') }}" method="GET" class="flex gap-4 my-6 flex justify-center">
        <input type="text" name="search" placeholder="Search by Course Name or Description"
            value="{{ request('search') }}" class="border px-3 py-2 rounded w-1/2">

        <select name="type" class="border pl-3 pr-12 py-2 rounded">
            <option value="">Semua Type</option>
            <option value="public" {{ request('type') == 'public' ? 'selected' : '' }}>Public</option>
            <option value="private" {{ request('type') == 'private' ? 'selected' : '' }}>Private</option>
        </select>

        <x-primary-button type="submit" class="bg-green-400 text-white px-4 py-2 rounded">Filter</x-primary-button>

    </form>
    <div class="flex justify-start gap-12 px-20">
        @forelse ($courses as $course)
            <div class="bg-white w-[400px] relative shadow-md rounded-lg overflow-hidden cursor-pointer">
                <h3 class="absolute top-4 left-4 text-[16px] font-bold bg-green-400 text-white px-2 py-1 rounded inline-block max-w-[33%] truncate">{{ $course->owner->name  }}</h3>
                <img src="{{ $course->cover_url }}" alt="{{ $course->title }}" class="w-[400px] h-[160px] object-cover">
                <div class="p-4 w-full">
                    <h3 class="text-lg font-semibold truncate">
                        {{ $course->title }}
                    </h3>
                    <p class="text-gray-600 text-sm mb-2 truncate">
                        {{ $course->description }}
                    </p>
                    <a href="{{ route('course.show', $course->course_id) }}"
                        class="inline-block bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded">
                        Lihat Detail
                    </a>
                </div>

            </div>
        @empty
            <h3 class="text-[16px] font-normal text-gray-500 py-4 mx-auto">No Course Available</h3>
        @endforelse
    </div>

</x-app-layout>
