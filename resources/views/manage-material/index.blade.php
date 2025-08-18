<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Your Course Materials : ' . $course->title . '') }}
        </h2>
    </x-slot>

    <div class="w-[90%] mx-auto">
        <div class="card shadow-sm border rounded-3">
            <div class="table-responsive">
                <form action="{{ route('manage-course.index') }}" method="GET" class="flex gap-4 my-6">
                    <input type="text" name="search" placeholder="Search by Course Name or Description"
                        value="{{ request('search') }}" class="border px-3 py-2 rounded w-1/2">

                    {{-- <select name="type" class="border pl-3 pr-12 py-2 rounded">
                        <option value="">All Type</option>
                        <option value="private" {{ request('type') == 'private' ? 'selected' : '' }}>Private</option>
                        <option value="public" {{ request('type') == 'public' ? 'selected' : '' }}>Public</option>
                    </select> --}}

                    <x-primary-button type="submit"
                        class="bg-green-400 text-white px-4 py-2 rounded">Filter</x-primary-button>
                    @role('teacher')
                        <a href="{{ route('manage-material.create', $course->course_id) }}">
                            <x-primary-button type="button" class="bg-green-400 text-white px-4 py-2 h-full rounded">Create
                                New
                                Material</x-primary-button>
                        </a>
                    @endrole
                </form>
                <table
                    class="table table-bordered table-striped mb-0 flex justify-center w-full border-separate border-spacing-[1px]">
                    <thead class="bg-green-400">
                        <tr>
                            <th class="w-[50px] text-white">No</th>
                            <th class="text-white">Material Name</th>
                            <th class="text-white">Description</th>
                            <th class="text-white">Content</th>
                            <th class="text-white">File</th>
                            <th style="width: 170px;" class="text-center text-white">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white text-center">
                        @forelse ($course_materials as $index => $course_material)
                            <tr class="cursor-pointer">
                                <td class="text-center py-2">
                                    {{ $course_materials->currentPage() * 10 - 10 + $index + 1 }}</td>
                                <td>{{ $course_material->title }}</td>
                                <td>{{ $course_material->description }}</td>
                                <td>
                                    {{ $course_material->content }}
                                </td>

                                <td class="py-2">
                                    @php
                                        $fileUrl = asset('storage/' . $course_material->file_path);
                                        $extension = strtolower(
                                            pathinfo($course_material->file_path, PATHINFO_EXTENSION),
                                        );
                                    @endphp

                                    @if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                        <img src="{{ $fileUrl }}" alt="Preview"
                                            class="w-20 h-20 object-cover rounded mx-auto">
                                    @elseif (in_array($extension, ['mp4', 'webm', 'ogg']))
                                        <video class="w-32 h-20 mx-auto rounded" controls>
                                            <source src="{{ $fileUrl }}" type="video/{{ $extension }}">
                                            Your browser does not support the video tag.
                                        </video>
                                    @elseif (in_array($extension, ['pdf']))
                                        <a href="{{ $fileUrl }}" target="_blank"
                                            class="text-blue-500 underline">View PDF</a>
                                    @else
                                        <a href="{{ $fileUrl }}" target="_blank" class="text-blue-500 underline">
                                            Download File
                                        </a>
                                    @endif
                                </td>

                                <td>
                                    <div class="flex justify-center items-center gap-2">
                                        @if (Route::has('manage-material.edit'))
                                            <a class="w-1/3 py-2 px-0"
                                                href="{{ route('manage-material.edit', $course_material->course_material_id) }}">
                                                <x-bxs-edit class="w-6 h-6 mx-auto text-gray-400" />
                                            </a>
                                        @endif

                                        @if (Route::has('manage-material.destroy'))
                                            <button x-data class="w-1/3 py-2 px-0"
                                                @click="$dispatch('open-modal', 'delete_course_{{ $course_material->course_material_id }}')">
                                                <x-bxs-trash class="w-6 h-6 mx-auto text-gray-400" />
                                            </button>

                                            <x-confirm-modal id="delete_course_{{ $course_material->course_material_id }}"
                                                message="Are you sure you want to delete {{ $course_material->title }}?"
                                                okLabel="Delete" cancelLabel="Cancel" :url="route('manage-material.destroy', $course_material->course_material_id)"
                                                method="DELETE" />
                                        @endif
                                    </div>

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    No Material found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="my-8">
                    {{ $course_materials->withQueryString()->links() }}
                </div>

            </div>
        </div>
    </div>


</x-app-layout>
