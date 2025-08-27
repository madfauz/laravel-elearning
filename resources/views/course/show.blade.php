<x-app-layout>
    <div class="w-full mx-auto py-8 px-4 md:px-16">
        @if ($course->cover_path)
            <div class="w-full h-[200px] md:h-[360px] overflow-hidden rounded-xl relative">
                <img class="rounded-md w-full h-full object-cover" src="{{ $course->cover_url }}"
                    alt="Cover {{ $course->title }}" />
                <div class="absolute bottom-8 left-8 z-30 w-[80%]">
                    <h3 class="text-[32px] font-bold text-white">{{ $course->title }}</h3>
                    <p class="text-white text-[16px] my-2 line-clamp-2 w-[100%]">
                        {{ $course->description }}
                    </p>
                    @if (Route::has('course.enrollment.destroy') && auth()->user()->user_id !== $course->teacher_id)
                        <button x-data
                            class="w-auto py-2 px-0 px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition-[0.3s] flex items-center gap-2"
                            @click="$dispatch('open-modal', 'leave_course_{{ $course->course_id }}')">
                            <x-bxs-trash class="w-4 h-4 text-white" />
                            <h4 class="text-[12px]">Leave Course</h4>
                        </button>

                        <x-confirm-modal id="leave_course_{{ $course->course_id }}"
                            message="Are you sure you want to leave {{ $course->title }}?" okLabel="Delete" cancelLabel="Cancel"
                            :url="route('course.enrollment.destroy', $course->course_id)" method="DELETE" />
                    @endif
                </div>

                <div class="bg-black absolute w-full h-full top-0 left-0 z-10"
                    style="background: linear-gradient(to bottom, rgba(0,0,0,0) 0%, rgba(0,0,0,1) 100%);"></div>
            </div>
        @endif

        <div class="my-6">

            @forelse ($course->content_items as $item)
                <div class="mb-6 p-4 border rounded-md bg-white shadow-sm">
                    {{-- Header with content type badge --}}
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            @if ($item->content_type === 'material')
                                <span
                                    class="flex gap-1 items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <x-bxs-file-doc class="w-4 h-4 text-blue-800" />
                                    <h4>
                                        Material
                                    </h4>
                                </span>
                            @elseif($item->content_type === 'quiz')
                                <span
                                    class="flex gap-1 items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <x-bx-bulb class="w-4 h-4 text-green-800" />
                                    <h4>
                                        Quiz
                                    </h4>
                                </span>
                            @endif
                            <h4 class="text-md font-semibold">{{ $item->title }}</h4>
                        </div>
                        <span class="text-xs text-gray-500">{{ $item->created_at->format('M d, Y') }}</span>
                    </div>

                    {{-- Content based on type --}}
                    @if ($item->content_type === 'material')
                        {{-- Material Content --}}
                        @if ($item->description)
                            <p class="text-gray-600 mb-2 whitespace-pre-line">{{ $item->description }}</p>
                        @endif

                        @if ($item->content_text)
                            <div class="mb-2 text-gray-800 whitespace-pre-line">{{ $item->content_text }}</div>
                        @endif

                        @if ($item->file_path)
                            @php
                                $fileUrl = $item->file_path;
                                $extension = pathinfo($item->file_path, PATHINFO_EXTENSION);
                            @endphp

                            @if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']))
                                <img src="{{ $fileUrl }}" alt="{{ $item->title }}" class="max-w-full h-auto rounded" />
                            @elseif(in_array(strtolower($extension), ['mp4', 'webm', 'ogg']))
                                <video controls class="max-w-full rounded">
                                    <source src="{{ $fileUrl }}" type="video/{{ $extension }}">
                                    Your browser does not support the video tag.
                                </video>
                            @elseif(in_array(strtolower($extension), ['pdf']))
                                <a href="{{ $fileUrl }}" target="_blank" class="text-indigo-600 underline">View
                                    PDF Document</a>
                            @else
                                <a href="{{ $fileUrl }}" target="_blank" class="text-indigo-600 underline">Download File</a>
                            @endif
                        @endif
                    @elseif($item->content_type === 'quiz')
                        {{-- Quiz Content --}}
                        @if ($item->description)
                            <p class="text-gray-600 mb-3 whitespace-pre-line">{{ $item->description }}</p>
                        @endif

                        {{-- Quiz Info --}}
                        <div class="bg-gray-50 rounded-lg p-3 mb-3">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-2 text-sm text-gray-600">
                                @if (isset($item->duration) && $item->duration)
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Duration: {{ $item->duration }} minutes
                                    </div>
                                @endif

                                @if (isset($item->questions_count) && $item->questions_count)
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                            </path>
                                        </svg>
                                        {{ $item->questions_count }} Questions
                                    </div>
                                @endif

                                @if (isset($item->max_attempts) && $item->max_attempts)
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                            </path>
                                        </svg>
                                        Max {{ $item->max_attempts }} attempts
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Quiz Actions --}}
                        <div class="flex gap-2">
                            @role('student')
                            <a href="{{ route('quiz.show', ['course_id' => $course->course_id, 'quiz_id' => $item->quiz_id]) }}"
                                class="flex gap-2 items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition-colors">
                                <x-bx-target-lock class="w-5 h-5 text-white" />
                                <h4>
                                    Take Quiz
                                </h4>
                            </a>
                            @endrole

                            @if (auth()->check() && auth()->id() === $course->teacher_id)
                                <a href="{{ route('manage-quiz.edit', ['course_id' => $course->course_id, 'quiz_id' => $item->quiz_id]) }}"
                                    class="flex gap-2 items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md transition-colors">
                                    <x-bxs-edit class="w-5 h-5 text-white" />
                                    <h4>
                                        Edit Quiz
                                    </h4>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-8">
                    <x-bxs-file-doc class="w-12 h-12 text-gray-400 mx-auto" />
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No content available</h3>
                    <p class="mt-1 text-sm text-gray-500">No materials or quizzes have been added to this course yet.
                    </p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>