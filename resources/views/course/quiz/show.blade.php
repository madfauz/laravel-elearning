<x-app-layout>
    <div class="min-h-screen bg-gray-50">
        {{-- Header Quiz + Timer --}}
        <div class="bg-white shadow-sm border-b sticky top-0 z-10">
            <div class="max-w-4xl mx-auto px-4 py-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">{{ $quiz->title }}</h1>
                        <p class="text-sm text-gray-600">{{ $quiz->course->title }}</p>
                    </div>

                    @php
                        $now = \Carbon\Carbon::now('Asia/Jakarta');
                        if ($quiz->time_limit) {
                            $deadline = \Carbon\Carbon::parse($attempt->started_at, 'Asia/Jakarta')->addMinutes(
                                $quiz->time_limit,
                            );
                        } else {
                            $deadline = \Carbon\Carbon::parse($quiz->end_time, 'Asia/Jakarta');
                        }
                        $remainingSeconds = max(0, $now->diffInSeconds($deadline, false));
                    @endphp

                    @if ($quiz->time_limit || $quiz->end_time)
                        <div class="flex items-center space-x-4">
                            <div id="timer" class="text-lg font-mono font-bold text-red-600"
                                data-remaining="{{ $remainingSeconds }}">
                                --:--:--
                            </div>
                            <div class="text-sm text-gray-600">remaining</div>
                        </div>
                    @endif
                </div>

                {{-- Progress Bar --}}
                <div class="mt-4">
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div id="progress-bar" class="bg-blue-600 h-2.5 rounded-full" style="width: 0%"></div>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">
                        <span id="progress-text">0</span> / {{ $quiz->questions->count() }} questions
                    </p>
                </div>
            </div>
        </div>

        {{-- Navigation Pills --}}
        <div class="max-w-4xl mx-auto px-4 py-4">
            <div class="bg-white rounded-lg shadow-sm p-4">
                <p class="text-sm text-gray-600 mb-3">Quick Navigation:</p>
                <div class="flex flex-wrap gap-2">
                    @foreach ($quiz->questions as $index => $question)
                        <button type="button" onclick="showQuestion({{ $index }})"
                            class="question-nav w-10 h-10 rounded-lg border-2 flex items-center justify-center text-sm font-medium transition-colors border-gray-300 hover:border-blue-300"
                            data-question="{{ $index }}">
                            {{ $index + 1 }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Quiz Form --}}
        <div class="max-w-4xl mx-auto px-4 pb-8">
            <form id="quiz-form"
                action="{{ route('quiz.submit', ['course_id' => $quiz->course_id, 'quiz_id' => $quiz->quiz_id, 'attempt_id' => $attempt->attempt_id]) }}"
                method="POST">
                @csrf

                {{-- Questions --}}
                @foreach ($quiz->questions as $index => $question)
                    <div class="question-container bg-white rounded-lg shadow-sm p-6 mb-6 {{ $index === 0 ? '' : 'hidden' }}"
                        data-question="{{ $index }}">

                        {{-- Question Header --}}
                        <div class="flex justify-between items-start mb-6">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                    Question {{ $index + 1 }} of {{ $quiz->questions->count() }}
                                </h3>
                                <p class="text-gray-700 leading-relaxed">{{ $question->question_text }}</p>
                            </div>
                            <span
                                class="ml-4 px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full whitespace-nowrap">
                                {{ ucfirst(str_replace('_', ' ', $question->type)) }}
                            </span>
                        </div>

                        {{-- Answer Options --}}
                        <div class="mb-8">
                            @if ($question->type === 'multiple_choice' || $question->type === 'true_false')
                                <div class="space-y-3">
                                    @foreach ($question->options as $optionIndex => $option)
                                        <label
                                            class="flex items-start space-x-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                            <input type="radio" name="question_{{ $question->question_id }}"
                                                value="{{ $option->option_id }}"
                                                class="mt-1 text-blue-600 focus:ring-blue-500"
                                                onchange="onAnswerChange()">
                                            <div class="flex-1">
                                                <span class="text-gray-900">{{ chr(65 + $optionIndex) }}.</span>
                                                <span class="ml-2 text-gray-700">{{ $option->option_text }}</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @endif

                            @if ($question->type === 'short_answer')
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Your Answer:</label>
                                    <textarea name="question_{{ $question->question_id }}" rows="4"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Type your answer here..." onchange="onAnswerChange()" oninput="onAnswerChange()"></textarea>
                                </div>
                            @endif
                        </div>

                        {{-- Navigation Buttons --}}
                        <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                            <button type="button" onclick="previousQuestion()"
                                class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed {{ $index === 0 ? 'invisible' : '' }}">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Previous
                            </button>

                            <span class="text-sm text-gray-500">
                                {{ $index + 1 }} / {{ $quiz->questions->count() }}
                            </span>

                            @if ($index === $quiz->questions->count() - 1)
                                <button type="button" onclick="showSubmitModal()"
                                    class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                                    Submit Quiz
                                    <svg class="w-4 h-4 inline ml-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </button>
                            @else
                                <button type="button" onclick="nextQuestion()"
                                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                                    Next
                                    <svg class="w-4 h-4 inline ml-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </form>
        </div>
    </div>

    {{-- Submit Confirmation Modal --}}
    <div id="submitModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md mx-4 shadow-xl">
            <div class="flex items-center mb-4">
                <svg class="w-6 h-6 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z">
                    </path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900">Submit Quiz?</h3>
            </div>

            <p class="text-gray-600 mb-4">
                Are you sure you want to submit your quiz? You won't be able to change your answers after submission.
            </p>

            <div id="incomplete-questions" class="hidden mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-yellow-800 text-sm font-medium mb-1">⚠️ Warning: You have unanswered questions!</p>
                <p class="text-yellow-700 text-sm">Question numbers: <span id="incomplete-list"
                        class="font-medium"></span></p>
            </div>

            <div class="flex space-x-3">
                <button type="button" onclick="hideSubmitModal()"
                    class="flex-1 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    Cancel
                </button>
                <button type="button" onclick="submitQuiz()"
                    class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                    Submit
                </button>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    let currentQuestion = 0;
    let totalQuestions = {{ $quiz->questions->count() }};
    let timerInterval;

    // Initialize when page loads
    document.addEventListener("DOMContentLoaded", () => {
        initializeTimer();
        showQuestion(0);
        // Initial progress update
        updateProgress();
    });

    // Handle answer changes
    function onAnswerChange() {
        updateQuestionNavigation();
        updateProgress();
    }

    function showQuestion(index) {
        // Hide all question containers
        document.querySelectorAll('.question-container').forEach(container => {
            container.classList.add('hidden');
        });

        // Show selected question
        const targetContainer = document.querySelector(`[data-question="${index}"].question-container`);
        if (targetContainer) {
            targetContainer.classList.remove('hidden');
            currentQuestion = index;
            updateQuestionNavigation();
            updateProgress();
        }
    }

    function nextQuestion() {
        if (currentQuestion < totalQuestions - 1) {
            showQuestion(currentQuestion + 1);
        }
    }

    function previousQuestion() {
        if (currentQuestion > 0) {
            showQuestion(currentQuestion - 1);
        }
    }

    function updateQuestionNavigation() {
        document.querySelectorAll('.question-nav').forEach((btn, index) => {
            // Reset classes
            btn.className =
                'question-nav w-10 h-10 rounded-lg border-2 flex items-center justify-center text-sm font-medium transition-colors';

            if (index === currentQuestion) {
                btn.classList.add('border-blue-500', 'bg-blue-500', 'text-white');
            } else if (isQuestionAnswered(index)) {
                btn.classList.add('bg-green-500', 'border-green-500', 'text-white');
            } else {
                btn.classList.add('border-gray-300', 'hover:border-blue-300');
            }
        });
    }

    function updateProgress() {
        const progressBar = document.getElementById("progress-bar");
        const progressText = document.getElementById("progress-text");

        if (progressBar && progressText) {
            const answeredCount = getAnsweredQuestionsCount();
            const percent = (answeredCount / totalQuestions) * 100;

            progressBar.style.width = `${percent}%`;
            progressText.textContent = answeredCount;

            progressBar.className = 'h-2.5 rounded-full transition-all duration-300';
            if (percent === 100) {
                progressBar.classList.add('bg-green-600');
            } else if (percent >= 50) {
                progressBar.classList.add('bg-blue-600');
            } else {
                progressBar.classList.add('bg-blue-400');
            }
        }
    }

    function isQuestionAnswered(questionIndex) {
        const container = document.querySelector(`[data-question="${questionIndex}"].question-container`);
        if (!container) return false;

        const radioInputs = container.querySelectorAll('input[type="radio"]');
        const textareas = container.querySelectorAll('textarea');

        // Check radio buttons
        for (let input of radioInputs) {
            if (input.checked) return true;
        }

        // Check textareas
        for (let textarea of textareas) {
            if (textarea.value.trim() !== '') return true;
        }

        return false;
    }

    function getAnsweredQuestionsCount() {
        let count = 0;
        for (let i = 0; i < totalQuestions; i++) {
            if (isQuestionAnswered(i)) count++;
        }
        return count;
    }

    function getUnansweredQuestions() {
        let unanswered = [];
        for (let i = 0; i < totalQuestions; i++) {
            if (!isQuestionAnswered(i)) {
                unanswered.push(i + 1);
            }
        }
        return unanswered;
    }

    function showSubmitModal() {
        const modal = document.getElementById('submitModal');
        const incompleteDiv = document.getElementById('incomplete-questions');
        const incompleteList = document.getElementById('incomplete-list');

        const unanswered = getUnansweredQuestions();

        if (unanswered.length > 0) {
            incompleteDiv.classList.remove('hidden');
            incompleteList.textContent = unanswered.join(', ');
        } else {
            incompleteDiv.classList.add('hidden');
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function hideSubmitModal() {
        const modal = document.getElementById('submitModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function submitQuiz() {
        const form = document.getElementById('quiz-form');
        if (form) {
            // Clear timer
            if (timerInterval) {
                clearInterval(timerInterval);
            }
            form.submit();
        }
    }

    function initializeTimer() {
        const timerEl = document.getElementById("timer");
        if (!timerEl) return;

        let remaining = parseInt(timerEl.dataset.remaining, 10);

        function updateTimer() {
            if (remaining <= 0) {
                timerEl.textContent = "00:00:00";
                timerEl.className = "text-lg font-mono font-bold text-red-600";

                // Auto submit when time's up
                alert("Time's up! The quiz will be submitted automatically.");
                submitQuiz();
                return;
            }

            const hours = String(Math.floor(remaining / 3600)).padStart(2, "0");
            const minutes = String(Math.floor((remaining % 3600) / 60)).padStart(2, "0");
            const seconds = String(remaining % 60).padStart(2, "0");
            timerEl.textContent = `${hours}:${minutes}:${seconds}`;

            // Reset classes
            timerEl.className = "text-lg font-mono font-bold";

            // Color coding based on time remaining
            if (remaining > 3600) {
                timerEl.classList.add("text-green-600");
            } else if (remaining > 1800) {
                timerEl.classList.add("text-yellow-600");
            } else if (remaining > 300) {
                timerEl.classList.add("text-orange-600");
            } else {
                timerEl.classList.add("text-red-600");
            }

            remaining--;
        }

        updateTimer();
        timerInterval = setInterval(updateTimer, 1000);
    }

    // Auto-save functionality (optional)
    function autoSave() {
        // This could save progress to localStorage or make AJAX calls
        // Implementation depends on your requirements
    }

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowLeft' && currentQuestion > 0) {
            previousQuestion();
        } else if (e.key === 'ArrowRight' && currentQuestion < totalQuestions - 1) {
            nextQuestion();
        }
    });
</script>
