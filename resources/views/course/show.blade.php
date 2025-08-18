<x-app-layout>
    <div class="w-full mx-auto py-8 px-16">
        @if ($course->cover_path)
            <div class="w-full h-[360px] overflow-hidden rounded-xl relative">
                <img class="rounded-md mb-6 w-full h-full  object-cover"
                    src="{{ $course->cover_url }}" alt="Cover {{ $course->title }}" />
                <div class="absolute bottom-8 left-8 z-30">
                    <h3 class="text-[32px] font-bold mb-2 text-white">{{ $course->title }}</h3>
                    <p class="text-white text-[16px]">{{ $course->description }}</p>
                </div>

                <div class="bg-black absolute w-full h-full top-0 left-0 z-10"
                    style="background: linear-gradient(to bottom, rgba(0,0,0,0) 0%, rgba(0,0,0,1) 100%);"></div>
            </div>
        @endif

        <div class="my-6">

            @forelse ($course->materials as $material)
                <div class="mb-6 p-4 border rounded-md bg-white shadow-sm">
                    <h4 class="text-md font-semibold">{{ $material->title }}</h4>

                    @if ($material->description)
                        <p class="text-gray-600 mb-2 whitespace-pre-line">{{ $material->description }}</p>
                    @endif

                    @if ($material->content_text)
                        <div class="mb-2 text-gray-800 whitespace-pre-line">{{ $material->content_text }}</div>
                    @endif

                    @if ($material->file_path)
                        @php
                            $fileUrl = $material->file_path;
                            $extension = pathinfo($material->file_path, PATHINFO_EXTENSION);
                        @endphp

                        @if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']))
                            <img src="{{ $fileUrl }}" alt="{{ $material->title }}"
                                class="max-w-full h-auto rounded" />
                        @elseif(in_array(strtolower($extension), ['mp4', 'webm', 'ogg']))
                            <video controls class="max-w-full rounded">
                                <source src="{{ $fileUrl }}" type="video/{{ $extension }}">
                                Your browser does not support the video tag.
                            </video>
                        @elseif(in_array(strtolower($extension), ['pdf']))
                            <a href="{{ $fileUrl }}" target="_blank" class="text-indigo-600 underline">View PDF
                                Document</a>
                        @else
                            <a href="{{ $fileUrl }}" target="_blank" class="text-indigo-600 underline">Download
                                File</a>
                        @endif
                    @endif
                </div>
            @empty
                <p class="text-gray-500">No materials available for this course.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
