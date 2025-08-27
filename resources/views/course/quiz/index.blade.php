<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Find Quiz') }}
        </h2>
    </x-slot>

    <div class="card shadow-sm border rounded-3">
        <div class="w-[90%] mx-auto">
            <form action="{{ route('manage-quiz.index', ['course_id' => $course->course_id]) }}" method="GET"
                class="flex gap-4 my-6">
                <input type="text" name="search" placeholder="Search by name" value="{{ request('search') }}"
                    class="border px-3 py-2 rounded w-1/2">

                <select name="type" class="border pl-3 pr-12 py-2 rounded">
                    <option value="public" {{ request('type') == 'public' ? 'selected' : '' }}>Public</option>
                    <option value="private" {{ request('type') == 'private' ? 'selected' : '' }}>Private</option>
                </select>

                <div class="flex justify-between gap-4 w-[100%] md:w-auto">
                    <x-primary-button type="submit"
                        class="bg-green-400 text-white px-4 py-2 rounded">Filter</x-primary-button>
                    @role('teacher')
                        <a class="h-full rounded w-[50%] md:w-auto"
                            href="{{ route('manage-quiz.create', ['course_id' => $course->course_id]) }}">
                            <x-primary-button type="button" class="bg-green-400 text-white px-4 py-2 h-full">Create
                                New
                                Quiz</x-primary-button>
                        </a>
                    @endrole
                </div>
            </form>
        </div>

        <div class="overflow-x-auto md:w-[90%] mx-auto">
            <table
                class="ml-[5%] md:ml-0 table min-w-[640px] table-auto table-bordered table-striped mb-0 flex justify-center w-full border-separate border-spacing-[1px]">
                <thead class="bg-green-400">
                    <tr>
                        <th class="w-[50px] text-white">No</th>
                        <th class="text-white">Title</th>
                        <th class="text-white">Description</th>
                        <th class="text-white">Duration</th>
                        <th class="text-white">Question</th>
                        <th class="text-white">Closed on</th>
                        <th style="width: 170px;" class="text-center text-white">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white text-center">
                    @forelse ($quizzes as $index => $quiz)
                        <tr class="cursor-pointer">
                            <td>{{ $quizzes->currentPage() * 10 - 10 + $index + 1 }}</td>
                            <td>{{ $quiz->title }}</td>
                            <td>{{ $quiz->description }}</td>
                            <td>
                                @php
                                    $hours = intdiv($quiz->time_limit, 60);
                                    $minutes = $quiz->time_limit % 60;
                                @endphp

                                @if ($hours > 0)
                                    {{ $hours }} {{ $hours > 1 ? 'hours' : 'hour' }} 
                                @endif

                                @if ($minutes > 0)
                                    {{ $minutes }} {{ $minutes > 1 ? 'minutes' : 'minute' }}
                                @endif
                            </td>

                            <td>{{ $quiz->questions->count() }}</td>
                            <td>
                                {{ $quiz->end_time ? $quiz->end_time_formatted : '-' }}
                            </td>
                            <td class="flex justify-center gap-2">
                                @if (Route::has('manage-quiz.edit'))
                                    <a class="w-1/3 py-2 px-0"
                                        href="{{ route('manage-quiz.edit', ['course_id' => $quiz->course_id, 'quiz_id' => $quiz->quiz_id]) }}">
                                        <x-bxs-edit class="w-6 h-6 mx-auto text-gray-400" />
                                    </a>
                                @endif

                                @if (Route::has('manage-quiz.destroy'))
                                    <button x-data class="w-1/3 py-2 px-0"
                                        @click="$dispatch('open-modal', 'delete_quiz_{{ $quiz->quiz_id }}')">
                                        <x-bxs-trash class="w-6 h-6 mx-auto text-gray-400" />
                                    </button>

                                    <x-confirm-modal id="delete_quiz_{{ $quiz->quiz_id }}"
                                        message="Are you sure you want to delete {{ $quiz->title }}?" okLabel="Delete"
                                        cancelLabel="Cancel"
                                        url="{{ route('manage-quiz.destroy', ['course_id' => $quiz->course_id, 'quiz_id' => $quiz->quiz_id]) }}"
                                        method="DELETE" />
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-[16px] font-normal text-gray-500 py-4">No Quizzes found</td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
            <div class="my-8">
                {{ $quizzes->withQueryString()->links() }}
            </div>

        </div>
    </div>

</x-app-layout>
