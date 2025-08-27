<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Find Available Course') }}
        </h2>
    </x-slot>

    <form action="{{ route('main.dashboard') }}" method="GET"
        class="flex flex-col md:flex-row justify-center items-center gap-4 my-6 mx-2 md:mx-12">
        <input type="text" name="search" placeholder="Search by Course Name or Description"
            value="{{ request('search') }}" class="border px-3 py-2 rounded w-[90%] md:w-2/3">

        <div class="flex justify-between md:justify-start gap-4 w-[90%] md:w-1/3">
            <select name="type" class="border pl-3 pr-12 py-2 rounded w-[70%] md:w-auto">
                <option value="">Semua Type</option>
                <option value="public" {{ request('type') == 'public' ? 'selected' : '' }}>Public</option>
                <option value="private" {{ request('type') == 'private' ? 'selected' : '' }}>Private</option>
            </select>

            <x-primary-button type="submit"
                class="bg-green-400 text-white px-4 py-2 rounded w-[30%] justify-center">Filter</x-primary-button>
        </div>

    </form>
    <div class="flex flex-col md:flex-row justify-center items-center md:justify-start gap-4 md:gap-12 mx-2 md:mx-12">
        @forelse ($courses as $course)
            <div class="bg-white w-[90%] md:w-[400px] relative shadow-md rounded-lg overflow-hidden cursor-pointer">
                <img class="absolute top-4 right-4 w-[40px] h-[40px] object-cover rounded-full"
                    src="{{ $course->teacher->file_url }}" alt="">
                <h3
                    class="absolute top-5 right-16 text-[16px] font-bold bg-gray-400 bg-opacity-40 text-white px-2 py-1 rounded inline-block max-w-[33%] truncate">
                    {{ $course->teacher->name }}</h3>

                <img src="{{ $course->cover_url }}" alt="{{ $course->title }}" class="w-full h-[160px] object-cover">
                <div class="p-4 w-full">
                    <h3 class="text-[20px] font-semibold truncate">
                        {{ $course->title }}
                    </h3>
                    <p class="text-gray-600 text-sm truncate mb-2">
                        {{ $course->description }}
                    </p>

                    <a href="{{ route('course.show', $course->course_id) }}"
                        class="inline-block bg-green-400 hover:bg-green-700 text-white text-sm font-medium px-4 py-2 rounded">
                        Lihat Detail
                    </a>
                </div>

            </div>
        @empty
            <h3 class="text-[16px] font-normal text-gray-500 py-4 mx-auto">No Course Available</h3>
        @endforelse
    </div>

</x-app-layout>
