<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Find Course') }}
        </h2>
    </x-slot>
    <form action="{{ route('course.index') }}" method="GET" class="flex gap-4 my-6">
        <input type="text" name="search" placeholder="Search by name, username or email" value="{{ request('search') }}"
            class="border px-3 py-2 rounded w-1/2">

        <select name="type" class="border pl-3 pr-12 py-2 rounded">
            <option value="public" {{ request('type') == 'public' ? 'selected' : '' }}>Public</option>
            <option value="private" {{ request('type') == 'private' ? 'selected' : '' }}>Private</option>
        </select>

        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Filter</button>
    </form>
    <div class="row">
        @foreach ($courses as $course)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="{{ $course->cover_url }}" class="card-img-top" alt="{{ $course->title }}"
                        style="height:200px;object-fit:cover;">

                    <div class="card-body">
                        <h5 class="card-title">{{ $course->title }}</h5>
                        <p class="card-text">{{ Str::limit($course->description, 100) }}</p>
                        <a href="{{ $course->url }}" class="btn btn-sm btn-primary">Lihat Detail</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</x-app-layout>
