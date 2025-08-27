<x-app-layout>
    <div class="w-[90%] md:w-1/2 mx-auto my-12">
        <a href="{{ route('manage-material.index', $course->course_id) }}"
            class="text-sm text-gray-600 flex items-center gap-2 mb-6 cursor-pointer">
            <x-bx-arrow-back class="w-4 h-4 text-gray-600" />
            {{ __('Back') }}
        </a>
        <form id="create_material_course" method="POST" action="{{ route('manage-material.store', $course->course_id) }}"
            enctype="multipart/form-data">
            @csrf

            <div>
                <x-input-label for="title" :value="__('Material Name')" />
                <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')"
                    required autofocus autocomplete="title" />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="description" :value="__('Description')" />
                <x-text-input id="description" class="block mt-1 w-full" type="text" name="description"
                    :value="old('description')" autofocus autocomplete="description" />
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="content_text" :value="__('Content')" />

                <textarea id="content_text" name="content_text" rows="5"
                    class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    autofocus autocomplete="content_text" style="resize: none">{{ old('content_text') }}</textarea>

                <x-input-error :messages="$errors->get('content_text')" class="mt-2" />
            </div>


            <div class="mt-4">
                <x-input-label for="file_path" :value="__('Thumbnail / Media')" />
                <input id="file_path"
                    class="block w-full px-4 py-2 mt-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm file:mr-4 file:py-2 file:px-4
           file:rounded-md file:border-0 file:text-sm file:font-semibold
           file:bg-green-500 file:text-white hover:file:bg-green-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    type="file" name="file_path" accept="image/*,video/*,.pdf,.doc,.docx,.ppt,.pptx" required />

                <div id="file_preview_wrapper" class="mt-2 hidden">
                    <p class="text-sm text-gray-500 mb-1">Preview:</p>

                    <img id="preview_image" src="" alt="Image Preview" class="max-w-xs rounded shadow hidden" />

                    <video id="preview_video" controls class="max-w-xs rounded shadow hidden"></video>

                    <a id="preview_file_link" href="#" target="_blank"
                        class="text-blue-600 underline text-sm hidden">Open File</a>
                </div>

                <x-input-error :messages="$errors->get('file_path')" class="mt-2" />
            </div>

        </form>

        @if (Route::has('manage-material.store'))
            <div class="flex items-center justify-end mt-4">
                <x-primary-button x-data @click="$dispatch('open-modal', 'save_confirmation')" class="ml-4">
                    {{ __('Save') }}
                </x-primary-button>
                <x-confirm-modal id="save_confirmation" message="Are you sure you want to create new Material?"
                    okLabel="Save" cancelLabel="Cancel" :url="route('manage-material.store', $course->course_id)" formId="create_material_course" />
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

            if (fileInput) {
                fileInput.addEventListener('change', function(event) {
                    const file = event.target.files[0];

                    previewImage.classList.add('hidden');
                    previewVideo.classList.add('hidden');
                    previewFileLink.classList.add('hidden');

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
