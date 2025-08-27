<?php

namespace App\Http\Controllers;

use App\Http\Requests\Quiz\SubmitQuizRequest;
use App\Models\CourseEnrollment;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Services\QuizService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class QuizAttemptController extends Controller
{
    protected QuizService $quizService;

    public function __construct(QuizService $quizService)
    {
        $this->quizService = $quizService;
    }

    public function show($course_id, $quiz_id)
    {
        try {
            $quiz = Quiz::with(['questions.options'])->findOrFail($quiz_id);

            if ($quiz->course_id !== $course_id) {
                abort(404);
            }

            $attempt = $this->quizService->startQuizAttempt($quiz, auth()->user());

            return view('course.quiz.show', compact('quiz', 'attempt'));

        } catch (\Exception $e) {
            flash($e->getMessage())->error();
            return redirect()->route('course.show', [$course_id]);
        }
    }

    public function start($course_id, $quiz_id)
    {
        $quiz = Quiz::findOrFail($quiz_id);

        if ($quiz->course_id !== $course_id) {
            abort(404);
        }

        try {
            $attempt = $this->quizService->startQuizAttempt($quiz, auth()->user());

            return redirect()->route('course.quiz.attempt', [$course_id, $quiz_id, $attempt->attempt_id]);

        } catch (\Exception $e) {
            flash($e->getMessage())->error();
            return redirect()->back();
        }
    }

    public function attempt($course_id, $quiz_id, $attempt_id)
    {
        $attempt = QuizAttempt::with(['quiz.questions.options'])->findOrFail($attempt_id);

        if ($attempt->student_id !== auth()->id() || $attempt->quiz_id !== $quiz_id) {
            abort(403);
        }

        if ($attempt->finished_at) {
            return redirect()->route('course.quiz.result', [$course_id, $quiz_id, $attempt_id]);
        }

        return view('quiz.attempt', compact('attempt'));
    }

    public function submit(SubmitQuizRequest $request, $course_id, $quiz_id, $attempt_id)
    {
        $attempt = QuizAttempt::findOrFail($attempt_id);

        if ($attempt->student_id !== auth()->id()) {
            abort(403);
        }

        try {
            $result = $this->quizService->submitQuizAnswers($attempt, $request->validated()['answers']);

            flash("Quiz submitted! Your score: {$result['score']}%")->success();
            return redirect()->route('course.quiz.result', [$course_id, $quiz_id, $attempt_id]);

        } catch (\Exception $e) {
            flash($e->getMessage())->error();
            return redirect()->back()->withInput();
        }
    }

    public function result($course_id, $quiz_id, $attempt_id)
    {
        $attempt = QuizAttempt::with(['quiz.questions.options', 'answers.question', 'answers.option'])
            ->findOrFail($attempt_id);

        if ($attempt->student_id !== auth()->id()) {
            abort(403);
        }

        if (!$attempt->finished_at) {
            return redirect()->route('course.quiz.attempt', [$course_id, $quiz_id, $attempt_id]);
        }

        return view('quiz.result', compact('attempt'));
    }

    public function history($course_id, $quiz_id)
    {
        $quiz = Quiz::findOrFail($quiz_id);

        $results = $this->quizService->getStudentQuizResults($quiz, auth()->user());

        return view('quiz.history', $results);
    }
}
