<?php

namespace App\Services;

use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuizService
{
    /**
     * Create new quiz with questions and options
     */
    public function createQuiz(array $data): Quiz
    {
        DB::beginTransaction();

        try {
            $data['start_time'] = !empty($data['start_time'])
                ? Carbon::parse(str_replace('T', ' ', $data['start_time']))
                : now();

            $data['end_time'] = !empty($data['end_time'])
                ? Carbon::parse(str_replace('T', ' ', $data['end_time']))
                : now()->addDay();

            // Create quiz
            $quiz = Quiz::create([
                'quiz_id' => Str::uuid(),
                'course_id' => $data['course_id'],
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'time_limit' => $data['time_limit'] ?? null,
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
            ]);

            // Create questions and options
            if (isset($data['questions']) && is_array($data['questions'])) {
                $this->createQuestions($quiz, $data['questions']);
            }

            DB::commit();
            return $quiz->load(['questions.options']);
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Update existing quiz
     */
    public function updateQuiz(Quiz $quiz, array $data): Quiz
    {
        // Check if quiz has attempts
        if ($quiz->attempts()->exists()) {
            throw new \Exception('Cannot modify quiz that has been attempted by students');
        }

        DB::beginTransaction();
        try {
            // Update quiz basic info
            $quiz->update([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'time_limit' => $data['time_limit'] ?? null,
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
            ]);

            // Update questions if provided
            if (isset($data['questions'])) {
                // Delete existing questions (cascade will delete options)
                $quiz->questions()->delete();
                // Create new questions
                $this->createQuestions($quiz, $data['questions']);
            }

            DB::commit();
            return $quiz->load(['questions.options']);
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function deleteQuiz(Quiz $quiz): bool
    {
        if ($quiz->attempts()->exists()) {
            throw new \Exception('Cannot delete quiz that has been attempted by students');
        }

        DB::beginTransaction();
        try {
            $quiz->delete();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function startQuizAttempt(Quiz $quiz, User $student): QuizAttempt
    {
        // Check if student is enrolled in course
        if (!$student->enrolledCourses()->where('course_id', $quiz->course_id)->exists()) {
            throw new \Exception('You are not enrolled in this course');
        }

        // Check quiz availability
        $now = Carbon::now();
        if ($now->lt($quiz->start_time)) {
            throw new \Exception('Quiz is not yet available');
        }

        if ($now->gt($quiz->end_time)) {
            throw new \Exception('Quiz has expired');
        }

        // Check if student already has an active attempt
        $activeAttempt = $quiz->attempts()
            ->where('student_id', $student->user_id)
            ->whereNull('finished_at')
            ->first();

        if ($activeAttempt) {
            return $activeAttempt;
        }

        // Create new attempt
        $attempt = QuizAttempt::create([
            'attempt_id' => Str::uuid(),
            'quiz_id' => $quiz->quiz_id,
            'student_id' => $student->user_id,
            'started_at' => Carbon::now('Asia/Jakarta'),
        ]);

        return $attempt;
    }

    /**
     * Submit quiz answers
     */
    public function submitQuizAnswers(QuizAttempt $attempt, array $answers): array
    {
        if ($attempt->finished_at) {
            throw new \Exception('Quiz attempt has already been submitted');
        }

        // Check time limit
        if ($attempt->quiz->time_limit) {
            $timeElapsed = Carbon::parse($attempt->started_at)->diffInMinutes(Carbon::now());
            if ($timeElapsed > $attempt->quiz->time_limit) {
                throw new \Exception('Time limit exceeded');
            }
        }

        DB::beginTransaction();
        try {
            $score = 0;
            $totalQuestions = $attempt->quiz->questions()->count();

            foreach ($answers as $answerData) {
                $question = QuizQuestion::findOrFail($answerData['question_id']);
                $isCorrect = $this->checkAnswer($question, $answerData);

                if ($isCorrect) {
                    $score++;
                }

                // Save answer
                QuizAnswer::create([
                    'answer_id' => Str::uuid(),
                    'attempt_id' => $attempt->attempt_id,
                    'question_id' => $answerData['question_id'],
                    'option_id' => $answerData['option_id'] ?? null,
                    'answer_text' => $answerData['answer_text'] ?? null,
                    'is_correct' => $isCorrect,
                ]);
            }

            // Calculate final score percentage
            $finalScore = $totalQuestions > 0 ? round(($score / $totalQuestions) * 100) : 0;

            // Update attempt
            $attempt->update([
                'score' => $finalScore,
                'finished_at' => Carbon::now(),
            ]);

            DB::commit();

            return [
                'score' => $finalScore,
                'correct_answers' => $score,
                'total_questions' => $totalQuestions,
                'attempt' => $attempt->load(['answers.question', 'answers.option']),
            ];
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Get quiz results for student
     */
    public function getStudentQuizResults(Quiz $quiz, User $student): array
    {
        $attempts = $quiz
            ->attempts()
            ->where('student_id', $student->user_id)
            ->whereNotNull('finished_at')
            ->with(['answers.question', 'answers.option'])
            ->orderByDesc('finished_at')
            ->get();

        return [
            'quiz' => $quiz,
            'attempts' => $attempts,
            'best_score' => $attempts->max('score') ?? 0,
            'average_score' => $attempts->avg('score') ?? 0,
            'attempt_count' => $attempts->count(),
        ];
    }

    /**
     * Get quiz statistics for teacher
     */
    public function getQuizStatistics(Quiz $quiz): array
    {
        $attempts = $quiz->attempts()->whereNotNull('finished_at')->get();

        return [
            'total_attempts' => $attempts->count(),
            'average_score' => $attempts->avg('score') ?? 0,
            'highest_score' => $attempts->max('score') ?? 0,
            'lowest_score' => $attempts->min('score') ?? 0,
            'completion_rate' => $this->calculateCompletionRate($quiz),
            'student_results' => $this->getStudentResults($quiz),
        ];
    }

    /**
     * Create questions for quiz
     */
    private function createQuestions(Quiz $quiz, array $questionsData): void
    {
        foreach ($questionsData as $questionData) {
            $question = QuizQuestion::create([
                'question_id' => Str::uuid(),
                'quiz_id' => $quiz->quiz_id,
                'question_text' => $questionData['question_text'],
                'type' => $questionData['type'],
            ]);

            // Multiple Choice
            if ($questionData['type'] === 'multiple_choice' && !empty($questionData['options'])) {
                foreach ($questionData['options'] as $optionData) {
                    QuizOption::create([
                        'option_id' => Str::uuid(),
                        'question_id' => $question->question_id,
                        'option_text' => $optionData['option_text'],
                        'is_correct' => $optionData['is_correct'] ?? false,
                    ]);
                }
            }

            // True/False (auto generate if not provided)
            if ($questionData['type'] === 'true_false') {
                $options = $questionData['options'] ?? [['option_text' => 'True', 'is_correct' => $questionData['answer'] ?? false], ['option_text' => 'False', 'is_correct' => !($questionData['answer'] ?? false)]];

                foreach ($options as $optionData) {
                    QuizOption::create([
                        'option_id' => Str::uuid(),
                        'question_id' => $question->question_id,
                        'option_text' => $optionData['option_text'],
                        'is_correct' => $optionData['is_correct'] ?? false,
                    ]);
                }
            }

            // Short Answer → tidak perlu options
            if ($questionData['type'] === 'short_answer') {
                // Tidak buat apa-apa, cukup simpan pertanyaan saja
                // (optional: kalau kamu mau simpan jawaban kunci bisa tambahkan field di table question)
            }
        }
    }

    /**
     * Check if answer is correct
     */
    private function checkAnswer(QuizQuestion $question, array $answerData): bool
    {
        switch ($question->type) {
            case 'multiple_choice':
            case 'true_false':
                if (!isset($answerData['option_id'])) {
                    return false;
                }
                $option = QuizOption::find($answerData['option_id']);
                return $option && $option->is_correct;

            case 'short_answer':
                if (!isset($answerData['answer_text'])) {
                    return false;
                }
                return true;

            default:
                return false;
        }
    }

    /**
     * Calculate completion rate
     */
    private function calculateCompletionRate(Quiz $quiz): float
    {
        $totalEnrolled = $quiz->course->enrollments()->count();
        if ($totalEnrolled === 0) {
            return 0;
        }

        $completedAttempts = $quiz->attempts()->whereNotNull('finished_at')->distinct('student_id')->count();

        return round(($completedAttempts / $totalEnrolled) * 100, 2);
    }

    /**
     * Get student results summary
     */
    private function getStudentResults(Quiz $quiz): array
    {
        return $quiz
            ->attempts()
            ->whereNotNull('finished_at')
            ->with('student')
            ->get()
            ->groupBy('student_id')
            ->map(function ($attempts) {
                $bestAttempt = $attempts->sortByDesc('score')->first();
                return [
                    'student' => $bestAttempt->student,
                    'best_score' => $bestAttempt->score,
                    'attempts_count' => $attempts->count(),
                    'last_attempt' => $attempts->sortByDesc('finished_at')->first()->finished_at,
                ];
            })
            ->values()
            ->toArray();
    }
}
