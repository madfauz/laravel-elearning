<x-app-layout>
    <div class="w-[90%] md:w-1/2 mx-auto my-12">
        <a href="{{ route('manage-material.index', $courseMaterial->course_id) }}"
            class="text-sm text-gray-600 flex items-center gap-2 mb-6 cursor-pointer">
            <x-bx-arrow-back class="w-4 h-4 text-gray-600" />
            {{ __('Back') }}
        </a>
        <form id="update_material_course" method="POST"
            action="{{ route('manage-material.update', $courseMaterial->course_material_id) }}"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div>
                <x-input-label for="title" :value="__('Material Name')" />
                <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $courseMaterial->title)"
                    required autofocus autocomplete="title" />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="description" :value="__('Description')" />
                <x-text-input id="description" class="block mt-1 w-full" type="text" name="description"
                    :value="old('description', $courseMaterial->description)" autofocus autocomplete="description" />
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="content_text" :value="__('Content')" />

                <textarea id="content_text" name="content_text" rows="5"
                    class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    autofocus autocomplete="content_text" style="resize: none">{{ old('content_text', $courseMaterial->content_text) }}</textarea>

                <x-input-error :messages="$errors->get('content_text')" class="mt-2" />
            </div>


            <div class="mt-4">
                <x-input-label for="file_path" :value="__('Thumbnail / Media')" />
                <input id="file_path" data-file="{{ $courseMaterial->file_path }}"
                    class="block w-full px-4 py-2 mt-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm file:mr-4 file:py-2 file:px-4
           file:rounded-md file:border-0 file:text-sm file:font-semibold
           file:bg-green-500 file:text-white hover:file:bg-green-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    type="file" name="file_path" accept="image/*,video/*,.pdf,.doc,.docx,.ppt,.pptx" />

                <div id="file_preview_wrapper" class="mt-2 {{ $courseMaterial->file_path ? '' : 'hidden' }}">
                    <p class="text-sm text-gray-500 mb-1">Preview:</p>

                    @php
                        $filePath = $courseMaterial->file_path;
                        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                    @endphp

                    {{-- Image --}}
                    <img id="preview_image"
                        src="{{ in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']) ? Storage::url($filePath) : '' }}"
                        alt="Image Preview"
                        class="max-w-xs rounded shadow {{ in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']) ? '' : 'hidden' }}" />

                    {{-- Video --}}
                    <video id="preview_video"
                        src="{{ in_array($ext, ['mp4', 'webm', 'ogg']) ? Storage::url($filePath) : '' }}" controls
                        class="max-w-xs rounded shadow {{ in_array($ext, ['mp4', 'webm', 'ogg']) ? '' : 'hidden' }}"></video>

                    {{-- File Link --}}
                    <a id="preview_file_link"
                        href="{{ !in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'webm', 'ogg']) && $filePath ? Storage::url($filePath) : '#' }}"
                        target="_blank"
                        class="text-blue-600 underline text-sm {{ !in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'webm', 'ogg']) && $filePath ? '' : 'hidden' }}">
                        Open File
                    </a>
                </div>

                <x-input-error :messages="$errors->get('file_path')" class="mt-2" />
            </div>

        </form>

        @if (Route::has('manage-material.update'))
            <div class="flex items-center justify-end mt-4">
                <x-primary-button x-data @click="$dispatch('open-modal', 'save_confirmation')" class="ml-4">
                    {{ __('Save') }}
                </x-primary-button>
                <x-confirm-modal id="save_confirmation" message="Are you sure you want to update this Material?"
                    okLabel="Save" cancelLabel="Cancel" :url="route('manage-material.update', $courseMaterial->course_material_id)" formId="update_material_course" />
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('file_path');
            const previewWrapper = document.getElementById('file_preview_wrapper');
            const previewImage = document.getElementById('preview_image');
            const previewVideo = document.getElementById('preview_video');
            const previewFileLink = document.getElementById('preview_file_link');

            function resetPreview() {
                if (previewImage) {
                    previewImage.src = '';
                    previewImage.classList.add('hidden');
                }
                if (previewVideo) {
                    previewVideo.src = '';
                    previewVideo.classList.add('hidden');
                }
                if (previewFileLink) {
                    previewFileLink.href = '#';
                    previewFileLink.textContent = '';
                    previewFileLink.classList.add('hidden');
                }
            }

            if (fileInput) {
                fileInput.addEventListener('change', function(event) {
                    const file = event.target.files[0];
                    resetPreview();

                    if (file) {
                        const fileType = file.type;
                        const reader = new FileReader();

                        if (fileType.startsWith('image/')) {
                            reader.onload = function(e) {
                                previewImage.src = e.target.result;
                                previewImage.classList.remove('hidden');
                                previewWrapper.classList.remove('hidden');
                            };
                            reader.readAsDataURL(file);

                        } else if (fileType.startsWith('video/')) {
                            reader.onload = function(e) {
                                previewVideo.src = e.target.result;
                                previewVideo.classList.remove('hidden');
                                previewWrapper.classList.remove('hidden');
                            };
                            reader.readAsDataURL(file);

                        } else {
                            const blobURL = URL.createObjectURL(file);
                            previewFileLink.href = blobURL;
                            previewFileLink.textContent = `Open ${file.name}`;
                            previewFileLink.classList.remove('hidden');
                            previewWrapper.classList.remove('hidden');
                        }
                    } else {
                        previewWrapper.classList.add('hidden');
                    }
                });
            }
        });
    </script>



</x-app-layout>
