<x-app-layout>
    <div class="w-[90%] md:w-1/2 mx-auto my-12">
        <a href="{{ route('manage-quiz.index', $course->course_id) }}"
            class="text-sm text-gray-600 flex items-center gap-2 mb-6 cursor-pointer">
            <x-bx-arrow-back class="w-4 h-4 text-gray-600" />
            {{ __('Back') }}
        </a>

        <form id="create_quiz_form" method="POST"
            action="{{ route('manage-quiz.store', ['course_id' => $course->course_id]) }}">
            @csrf

            {{-- Title --}}
            <div>
                <x-input-label for="title" :value="__('Quiz Title')" />
                <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')"
                    required autofocus autocomplete="title" />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>

            {{-- Description --}}
            <div class="mt-4">
                <x-input-label for="description" :value="__('Description')" />
                <textarea id="description" name="description" rows="3" style="resize:none"
                    class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('description') }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            {{-- Time Limit --}}
            <div class="mt-4">
                <x-input-label for="time_limit" :value="__('Time Limit (minutes)')" />
                <x-text-input id="time_limit" class="block mt-1 w-full" type="number" name="time_limit"
                    :value="old('time_limit')" min="1" />
                <x-input-error :messages="$errors->get('time_limit')" class="mt-2" />
            </div>

            {{-- Start Time & End Time --}}
            <div class="flex flex-col md:flex-row justify-between gap-8 mt-4">
                <div class="basis-1/2">
                    <x-input-label for="start_time" :value="__('Start Time')" />
                    <x-text-input id="start_time" class="block mt-1 w-full" type="datetime-local" name="start_time"
                        :value="old(
                            'start_time',
                            isset($quiz)
                                ? $quiz->start_time->format('Y-m-d\TH:i')
                                : now('Asia/Jakarta')->format('Y-m-d\TH:i'),
                        )" />
                    <x-input-error :messages="$errors->get('start_time')" class="mt-2" />
                </div>
                <div class="basis-1/2">
                    <x-input-label for="end_time" :value="__('End Time')" />
                    <x-text-input id="end_time" class="block mt-1 w-full" type="datetime-local" name="end_time"
                        :value="old(
                            'end_time',
                            isset($quiz)
                                ? $quiz->end_time->format('Y-m-d\TH:i')
                                : now('Asia/Jakarta')->addDay()->format('Y-m-d\TH:i'),
                        )" />
                    <x-input-error :messages="$errors->get('end_time')" class="mt-2" />
                </div>
            </div>

            {{-- Questions Section --}}
            <div class="mt-6">
                <h3 class="text-lg font-semibold mb-2">Questions</h3>
                <div id="questions_wrapper">
                    <div class="question_item border p-4 rounded mb-4 bg-gray-50" data-q-index="0"
                        data-next-opt-index="2">
                        <div class="flex flex-col items-start gap-3 h-[120px]">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Question Text</label>
                            <div class="flex justify-between items-center w-full gap-4">
                                <textarea name="questions[0][question_text]" rows="2" style="resize:none"
                                    class="block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                                <button type="button"
                                    class="remove-question text-gray-500 text-sm no-underline bg-gray-200 h-full px-2 rounded-md">Remove</button>
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                            <select name="questions[0][type]"
                                class="q-type block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="multiple_choice" selected>Multiple Choice</option>
                                <option value="true_false">True / False</option>
                                <option value="short_answer">Short Answer</option>
                            </select>
                        </div>

                        <div class="options-area mt-4">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-medium">Options</h4>
                                <button type="button"
                                    class="add-option px-2 py-1 text-xs bg-blue-400 text-white rounded hover:bg-blue-600 transition-[0.3s]">
                                    + Add Option
                                </button>
                            </div>

                            <div class="options-wrapper space-y-2">
                                <div class="option_item flex items-center gap-2" data-opt-index="0">
                                    <input type="text" name="questions[0][options][0][option_text]"
                                        class="flex-1 border-gray-300 rounded-md shadow-sm" placeholder="Option text">
                                    <label class="flex items-center gap-1 text-sm shrink-0">
                                        <input type="checkbox" class="correct-checkbox"
                                            name="questions[0][options][0][is_correct]" value="1">
                                        Correct
                                    </label>
                                    <button type="button"
                                        class="remove-option text-red-500 text-xs no-underline">Remove</button>
                                </div>

                                <div class="option_item flex items-center gap-2" data-opt-index="1">
                                    <input type="text" name="questions[0][options][1][option_text]"
                                        class="flex-1 border-gray-300 rounded-md shadow-sm" placeholder="Option text">
                                    <label class="flex items-center gap-1 text-sm shrink-0">
                                        <input type="checkbox" class="correct-checkbox"
                                            name="questions[0][options][1][is_correct]" value="1">
                                        Correct
                                    </label>
                                    <button type="button"
                                        class="remove-option text-red-500 text-xs no-underline">Remove</button>
                                </div>
                            </div>
                            <p class="hint text-xs text-gray-500 mt-1">
                                For True/False, only one option can be correct. For Short Answer, options are hidden.
                            </p>
                        </div>
                    </div>
                </div>

                <button type="button" id="add_question_btn"
                    class="px-4 py-2 text-white rounded shadow bg-gray-500 hover:bg-gray-900 transition-[0.3s]">
                    + Add Question
                </button>
            </div>

        </form>

        @if (Route::has('manage-quiz.store'))
            <div class="flex items-center justify-end mt-6">
                <x-primary-button x-data @click="$dispatch('open-modal', 'save_confirmation')"
                    class="px-4 py-2 ml-4 bg-green-500 hover:bg-green-700 transition-[0.3s]">
                    {{ __('Save Quiz') }}
                </x-primary-button>
                <x-confirm-modal id="save_confirmation" message="Are you sure you want to create this quiz?"
                    okLabel="Save" cancelLabel="Cancel" formId="create_quiz_form" />
            </div>
        @endif
    </div>

    <script>
        // Utility: build one option row
        function buildOptionRow(qIndex, optIndex, text = '', checked = false) {
            return `
            <div class="option_item flex items-center gap-2" data-opt-index="${optIndex}">
                <input type="text" name="questions[${qIndex}][options][${optIndex}][option_text]"
                       class="flex-1 border-gray-300 rounded-md shadow-sm"
                       placeholder="Option text" value="${text.replace(/"/g, '&quot;')}">
                <label class="flex items-center gap-1 text-sm shrink-0">
                    <input type="checkbox" class="correct-checkbox"
                           name="questions[${qIndex}][options][${optIndex}][is_correct]" value="1" ${checked ? 'checked' : ''}>
                    Correct
                </label>
                <button type="button" class="remove-option text-red-500 text-xs hover:underline">Remove</button>
            </div>
        `;
        }

        // Handle type change
        function handleTypeChange(questionEl) {
            const typeSelect = questionEl.querySelector('.q-type');
            const optionsArea = questionEl.querySelector('.options-area');
            const addBtn = questionEl.querySelector('.add-option');
            const optsWrapper = questionEl.querySelector('.options-wrapper');
            const qIndex = questionEl.dataset.qIndex;

            if (typeSelect.value === 'short_answer') {
                optionsArea.classList.add('hidden');
                return;
            }

            optionsArea.classList.remove('hidden');

            if (typeSelect.value === 'true_false') {
                // True/False: fixed 2 options
                addBtn.classList.add('hidden');
                optsWrapper.innerHTML = '';
                questionEl.dataset.nextOptIndex = '2';

                optsWrapper.insertAdjacentHTML('beforeend', buildOptionRow(qIndex, 0, 'True', true));
                optsWrapper.insertAdjacentHTML('beforeend', buildOptionRow(qIndex, 1, 'False', false));

                enforceSingleCorrect(questionEl);
            } else {
                // Multiple choice
                optsWrapper.innerHTML = optsWrapper.innerHTML || '';
                if (optsWrapper.children.length === 0) {
                    const next = Number(questionEl.dataset.nextOptIndex || 0);
                    optsWrapper.insertAdjacentHTML('beforeend', buildOptionRow(qIndex, next));
                    optsWrapper.insertAdjacentHTML('beforeend', buildOptionRow(qIndex, next + 1));
                    questionEl.dataset.nextOptIndex = String(next + 2);
                }
                releaseSingleCorrect(questionEl);
                updateAddOptionVisibility(questionEl);
            }
        }

        function enforceSingleCorrect(questionEl) {
            questionEl.querySelectorAll('.correct-checkbox').forEach(cb => {
                cb.addEventListener('change', () => {
                    if (cb.checked) {
                        questionEl.querySelectorAll('.correct-checkbox').forEach(other => {
                            if (other !== cb) other.checked = false;
                        });
                    }
                });
            });
        }

        function releaseSingleCorrect(questionEl) {
            const checkboxes = questionEl.querySelectorAll('.correct-checkbox');
            checkboxes.forEach(cb => {
                const clone = cb.cloneNode(true);
                cb.parentNode.replaceChild(clone, cb);
            });
        }

        function updateAddOptionVisibility(questionItem) {
            const addBtn = questionItem.querySelector('.add-option');
            if (!addBtn) return;
            const optionsCount = questionItem.querySelectorAll('.option_item').length;
            if (optionsCount >= 5) {
                addBtn.style.display = 'none';
            } else {
                addBtn.style.display = 'inline-block';
            }
        }

        function addOption(questionItem) {
            const wrapper = questionItem.querySelector('.options-wrapper');
            const qIndex = questionItem.dataset.qIndex;
            let optIndex = parseInt(questionItem.dataset.nextOptIndex || '0', 10);

            const newOption = document.createElement('div');
            newOption.classList.add('option_item', 'flex', 'items-center', 'gap-2');
            newOption.dataset.optIndex = optIndex;
            newOption.innerHTML = `
            <input type="text" name="questions[${qIndex}][options][${optIndex}][option_text]"
                   class="flex-1 border-gray-300 rounded-md shadow-sm"
                   placeholder="Option text">
            <label class="flex items-center gap-1 text-sm shrink-0">
                <input type="checkbox" class="correct-checkbox"
                       name="questions[${qIndex}][options][${optIndex}][is_correct]" value="1">
                Correct
            </label>
            <button type="button" class="remove-option text-red-500 text-xs no-underline">Remove</button>
        `;
            wrapper.appendChild(newOption);

            questionItem.dataset.nextOptIndex = optIndex + 1;
            updateAddOptionVisibility(questionItem);
        }

        function initQuestion(questionEl) {
            const typeSelect = questionEl.querySelector('.q-type');
            const addBtn = questionEl.querySelector('.add-option');
            const removeQuestionBtn = questionEl.querySelector('.remove-question');

            typeSelect.addEventListener('change', () => handleTypeChange(questionEl));
            addBtn?.addEventListener('click', () => addOption(questionEl));

            questionEl.addEventListener('click', (e) => {
                if (e.target && e.target.classList.contains('remove-option')) {
                    const opt = e.target.closest('.option_item');
                    opt?.remove();
                    updateAddOptionVisibility(questionEl);
                }
            });

            removeQuestionBtn?.addEventListener('click', () => {
                questionEl.remove();
            });

            handleTypeChange(questionEl);
            updateAddOptionVisibility(questionEl);
        }

        // Init first question if exists
        const firstQ = document.querySelector('.question_item');
        if (firstQ) {
            initQuestion(firstQ);
        }

        // Add new question
        let questionIndex = document.querySelectorAll('.question_item').length;
        document.getElementById('add_question_btn').addEventListener('click', function() {
            const wrapper = document.getElementById('questions_wrapper');
            const qEl = document.createElement('div');
            qEl.className = 'question_item border p-4 rounded mb-4 bg-gray-50';
            qEl.dataset.qIndex = String(questionIndex);
            qEl.dataset.nextOptIndex = '2';

            qEl.innerHTML = `
            <div class="flex items-start gap-3">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Question Text</label>
                    <textarea name="questions[${questionIndex}][question_text]" rows="2"
                              style="resize:none" class="block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                </div>
                <button type="button" class="remove-question text-red-600 text-sm mt-6 no-underline">Remove</button>
            </div>

            <div class="mt-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="questions[${questionIndex}][type]" class="q-type block w-full border-gray-300 rounded-md shadow-sm">
                    <option value="multiple_choice" selected>Multiple Choice</option>
                    <option value="true_false">True / False</option>
                    <option value="short_answer">Short Answer</option>
                </select>
            </div>

            <div class="options-area mt-4">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-medium">Options</h4>
                    <button type="button" class="add-option px-2 py-1 text-xs bg-blue-400 text-white rounded hover:bg-blue-600 transition-[0.3s]">+ Add Option</button>
                </div>

                <div class="options-wrapper space-y-2">
                    <div class="option_item flex items-center gap-2" data-opt-index="0">
                        <input type="text" name="questions[${questionIndex}][options][0][option_text]"
                               class="flex-1 border-gray-300 rounded-md shadow-sm"
                               placeholder="Option text">
                        <label class="flex items-center gap-1 text-sm shrink-0">
                            <input type="checkbox" class="correct-checkbox"
                                   name="questions[${questionIndex}][options][0][is_correct]" value="1">
                            Correct
                        </label>
                        <button type="button" class="remove-option text-red-500 text-xs no-underline">Remove</button>
                    </div>
                    <div class="option_item flex items-center gap-2" data-opt-index="1">
                        <input type="text" name="questions[${questionIndex}][options][1][option_text]"
                               class="flex-1 border-gray-300 rounded-md shadow-sm"
                               placeholder="Option text">
                        <label class="flex items-center gap-1 text-sm shrink-0">
                            <input type="checkbox" class="correct-checkbox"
                                   name="questions[${questionIndex}][options][1][is_correct]" value="1">
                            Correct
                        </label>
                        <button type="button" class="remove-option text-red-500 text-xs no-underline">Remove</button>
                    </div>
                </div>
                <p class="hint text-xs text-gray-500 mt-1">For True/False, only one option can be correct. For Short Answer, options are hidden.</p>
            </div>
        `;

            wrapper.appendChild(qEl);
            initQuestion(qEl);
            questionIndex++;
        });
    </script>
</x-app-layout>
