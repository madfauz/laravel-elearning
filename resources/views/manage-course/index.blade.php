<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Your Courses') }}
        </h2>
    </x-slot>

    <div class="w-[90%] mx-auto">
        <div class="card shadow-sm border rounded-3">
            <div class="table-responsive">
                <form action="{{ route('manage-course.index') }}" method="GET" class="flex gap-4 my-6">
                    <input type="text" name="search" placeholder="Search by Course Name or Description"
                        value="{{ request('search') }}" class="border px-3 py-2 rounded w-1/2">

                    <select name="type" class="border pl-3 pr-12 py-2 rounded">
                        <option value="">All Type</option>
                        <option value="private" {{ request('type') == 'private' ? 'selected' : '' }}>Private</option>
                        <option value="public" {{ request('type') == 'public' ? 'selected' : '' }}>Public</option>
                    </select>

                    <x-primary-button type="submit"
                        class="bg-green-400 text-white px-4 py-2 rounded">Filter</x-primary-button>
                    @role('teacher')
                        <a href="{{ route('course.create') }}">
                            <x-primary-button type="button" class="bg-green-400 text-white px-4 py-2 h-full rounded">Create
                                New
                                Course</x-primary-button>
                        </a>
                    @endrole
                </form>
                <table
                    class="table table-bordered table-striped mb-0 flex justify-center w-full border-separate border-spacing-[1px]">
                    <thead class="bg-green-400">
                        <tr>
                            <th class="w-[50px] text-white">No</th>
                            <th class="text-white">Course Name</th>
                            <th class="text-white">Member</th>
                            <th class="text-white">Material</th>
                            <th class="text-white">Type</th>
                            <th style="width: 170px;" class="text-center text-white">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white text-center">
                        @forelse ($courses as $index => $course)
                            <tr class="cursor-pointer">
                                <td class="text-center py-2">{{ $courses->currentPage() * 10 - 10 + $index + 1 }}</td>
                                <td>{{ $course->title }}</td>
                                <td>{{ $course->member_count }}</td>
                                <td>
                                    <div class="flex items-center justify-center gap-4">
                                        {{ $course->material_count }}
                                        @if (Route::has('manage-material.index'))
                                            <a class="w-auto" href="{{ route('manage-material.index', $course->course_id) }}">
                                                <x-bxs-edit class="w-6 h-6 text-gray-400" />
                                            </a>
                                        @endif
                                    </div>
                                </td>

                                <td><span
                                        class="{{ $course->type == 'private' ? 'bg-yellow-400' : 'bg-blue-400' }} px-2 py-1 rounded text-white badge">

                                        {{ ucfirst($course->type) }}</span></td>
                                <td class="flex justify-center gap-2">
                                    @if (Route::has('course.edit'))
                                        <a class="w-1/3 py-2 px-0"
                                            href="{{ route('course.edit', $course->course_id) }}">
                                            <x-bxs-edit class="w-6 h-6 mx-auto text-gray-400" />
                                        </a>
                                    @endif

                                    @if (Route::has('course.destroy'))
                                        <button x-data class="w-1/3 py-2 px-0"
                                            @click="$dispatch('open-modal', 'delete_course_{{ $course->course_id }}')">
                                            <x-bxs-trash class="w-6 h-6 mx-auto text-gray-400" />
                                        </button>

                                        <x-confirm-modal id="delete_course_{{ $course->course_id }}"
                                            message="Are you sure you want to delete {{ $course->title }}?"
                                            okLabel="Delete" cancelLabel="Cancel" :url="route('manage-user.destroy', $user->user_id)" method="DELETE" />
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-[16px] font-normal text-gray-500 py-4">
                                    No Courses found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="my-8">
                    {{ $courses->withQueryString()->links() }}
                </div>

            </div>
        </div>
    </div>


</x-app-layout>
