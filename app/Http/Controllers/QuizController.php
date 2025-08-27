<?php

namespace App\Http\Controllers;

use App\Http\Requests\Quiz\StoreQuizRequest;
use App\Http\Requests\Quiz\UpdateQuizRequest;
use App\Models\Course;
use App\Models\Quiz;
use App\Services\QuizService;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    protected QuizService $quizService;

    public function __construct(QuizService $quizService)
    {
        $this->quizService = $quizService;
    }

    public function index(Request $request, $course_id)
    {
        $course = Course::findOrFail($course_id);

        if ($course->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $quizzes = $course->quizzes()->with(['questions'])->paginate(10);

        return view('course.quiz.index', compact('course', 'quizzes'));
    }

    public function create($course_id)
    {
        $course = Course::findOrFail($course_id);

        if ($course->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        return view('course.quiz.create', compact('course'));
    }

    public function store(StoreQuizRequest $request, $course_id)
    {
        $course = Course::findOrFail($course_id);

        if ($course->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        try {
            $data = $request->validated();
            $data['course_id'] = $course_id;

            $quiz = $this->quizService->createQuiz($data);

            flash('Quiz created successfully')->success();
            return redirect()->route('manage-quiz.index', [$course_id, $quiz->quiz_id]);

        } catch (\Exception $e) {
            flash($e->getMessage())->error();
            return redirect()->back()->withInput();
        }
    }

    public function show($course_id, $quiz_id)
    {
        $quiz = Quiz::with(['course', 'questions.options'])->findOrFail($quiz_id);

        if ($quiz->course_id !== $course_id) {
            abort(404);
        }

        if ($quiz->course->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $statistics = $this->quizService->getQuizStatistics($quiz);

        return view('course.quiz.show', compact('quiz', 'statistics'));
    }

    public function edit($course_id, $quiz_id)
    {
        $quiz = Quiz::with(['questions.options'])->findOrFail($quiz_id);

        if ($quiz->course_id !== $course_id || $quiz->course->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        return view('course.quiz.edit', compact('quiz'));
    }

    public function update(UpdateQuizRequest $request, $course_id, $quiz_id)
    {
        $quiz = Quiz::findOrFail($quiz_id);

        if ($quiz->course_id !== $course_id || $quiz->course->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        try {
            $quiz = $this->quizService->updateQuiz($quiz, $request->validated());

            flash('Quiz updated successfully')->success();
            return redirect()->route('manage-quiz.index', [$course_id, $quiz->quiz_id]);

        } catch (\Exception $e) {
            flash($e->getMessage())->error();
            return redirect()->back()->withInput();
        }
    }

    public function destroy($course_id, $quiz_id)
    {
        $quiz = Quiz::findOrFail($quiz_id);

        if ($quiz->course_id !== $course_id || $quiz->course->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        try {
            $this->quizService->deleteQuiz($quiz);

            flash('Quiz deleted successfully')->success();
            return redirect()->route('manage-quiz.index', $course_id);

        } catch (\Exception $e) {
            flash($e->getMessage())->error();
            return redirect()->back();
        }
    }
}
