<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('List Task') }}
        </h2>
    </x-slot>

    <div class="w-[90%] mx-auto">
        <div class="card shadow-sm border rounded-3">
            <div class="table-responsive">
                <form action="{{ route('task.index') }}" method="GET" class="flex gap-4 my-6">
                    <input type="text" name="search" placeholder="Search by Task Name or Content"
                        value="{{ request('search') }}" class="border px-3 py-2 rounded w-1/2">

                    <x-primary-button type="submit"
                        class="bg-green-400 text-white px-4 py-2 rounded">Filter</x-primary-button>
                   
                        <a href="{{ route('task.create') }}">
                            <x-primary-button type="button" class="bg-green-400 text-white px-4 py-2 h-full rounded">Create
                                New
                                Task</x-primary-button>
                        </a>
                </form>
                <table
                    class="table table-bordered table-striped mb-0 flex justify-center w-full border-separate border-spacing-[1px]">
                    <thead class="bg-green-400">
                        <tr>
                            <th class="w-[50px] text-white">No</th>
                            <th class="text-white">Title</th>
                            <th class="text-white">Content</th>
                            <th class="text-white">File</th>
                            <th style="width: 170px;" class="text-center text-white">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white text-center">
                        @forelse ($tasks as $index => $task)
                            <tr class="cursor-pointer">
                                <td class="text-center py-2">{{ $tasks->currentPage() * 10 - 10 + $index + 1 }}</td>
                                <td>{{ $task->title }}</td>
                                <td>{{ $task->content }}</td>
                                <td>
                                   <img src="{{ $task->file_path ? $task->file_path : 'https://www.shutterstock.com/image-vector/default-ui-image-placeholder-wireframes-600nw-1037719192.jpg' }}" alt="" class="w-16 h-16 object-cover object-center mx-auto">
                                </td>
                                <td class="flex justify-center items-center gap-2">
                                    @if (Route::has('task.edit'))
                                        <a class="w-1/3 py-2 px-0"
                                            href="{{ route('task.edit', $task->task_id) }}">
                                            <x-bxs-edit class="w-6 h-6 mx-auto text-gray-400" />
                                        </a>
                                    @endif

                                    @if (Route::has('task.destroy'))
                                        <button x-data class="w-1/3 py-2 px-0"
                                            @click="$dispatch('open-modal', 'delete_course_{{ $task->task_id }}')">
                                            <x-bxs-trash class="w-6 h-6 mx-auto text-gray-400" />
                                        </button>

                                        <x-confirm-modal id="delete_course_{{ $task->task_id }}"
                                            message="Are you sure you want to delete {{ $task->title }}?"
                                            okLabel="Delete" cancelLabel="Cancel" :url="route('task.destroy', $task->task_id)" method="DELETE" />
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-[16px] font-normal text-gray-500 py-4">
                                    No Task found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="my-8">
                    {{ $tasks->withQueryString()->links() }}
                </div>

            </div>
        </div>
    </div>


</x-app-layout>