<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseMaterial;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourseService
{
    protected $fileService;

    public function __construct(FileStorageService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function getAllCourses(Request $request)
    {
        $query = Course::query()->with('teacher')->orderBy('updated_at');

        if ($request->routeIs('manage-course.index')) {
            $query->where('teacher_id', auth()->id());
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $type = $request->type;
            $query->where('type', $type);
        }

        $courses = $query->paginate(10);

        $courses->getCollection()->transform(function ($course) {
            return $this->transformCourseData($course);
        });

        return $courses;
    }

    public function getCourseById($course_id)
    {
        $course = Course::with([
            'teacher',
            'quizzes',
            'materials' => function ($query) {
                $query->orderBy('created_at');
            },
        ])
            ->where('course_id', $course_id)
            ->firstOrFail();

        $course->cover_url = $this->fileService->url($course->cover_path);

        foreach ($course->materials as $material) {
            $material->file_path = $this->fileService->url($material->file_path);
        }

        $course->content_items = $this->getCombinedCourseContent($course);

        return $course;
    }

    private function getCombinedCourseContent($course)
    {
        $materials = $course->materials->map(function ($material) {
            $material->content_type = 'material';
            return $material;
        });

        $quizzes = $course->quizzes->map(function ($quiz) {
            $quiz->content_type = 'quiz';
            return $quiz;
        });

        // Combine and sort by created_at (oldest first)
        return $materials->concat($quizzes)
            ->sortBy('created_at')
            ->values(); // Reset collection keys
    }

    public function getCourseForEdit($course_id)
    {
        $course = Course::with('teacher')->where('course_id', $course_id)->firstOrFail();
        $course->cover_url = $this->fileService->url($course->cover_path);

        return $course;
    }

    public function createCourse(array $validatedData)
    {
        $newStore = [
            'course_id' => uuid_create(),
            'teacher_id' => auth()->id(),
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'type' => $validatedData['type'] ?? 'public',
        ];

        // Handle access code
        if ($validatedData['type'] === 'private' && empty($validatedData['access_code'])) {
            $newStore['access_code'] = Str::upper(Str::random(8));
        } elseif ($validatedData['type'] === 'private') {
            $newStore['access_code'] = $validatedData['access_code'];
        } else {
            $newStore['access_code'] = null;
        }

        // Handle file upload
        if (isset($validatedData['cover_file'])) {
            $newStore['cover_path'] = $this->fileService->upload($validatedData['cover_file'], 'courses/covers');
        }

        return Course::create($newStore);
    }

    public function updateCourse($course_id, array $validatedData)
    {
        $course = Course::findOrFail($course_id);

        $updateData = [
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'type' => $validatedData['type'],
        ];

        if ($validatedData['type'] === 'private') {
            $updateData['access_code'] = $validatedData['access_code'] ?: Str::upper(Str::random(8));
        } else {
            $updateData['access_code'] = null;
        }

        if (isset($validatedData['cover_file'])) {
            if ($course->cover_path) {
                $this->fileService->delete($course->cover_path);
            }

            $updateData['cover_path'] = $this->fileService->upload($validatedData['cover_file'], 'courses/covers');
        }

        $course->update($updateData);

        return $course;
    }

    public function deleteCourse($course_id)
    {
        $course = Course::with('materials')->findOrFail($course_id);

        // Delete associated files
        if ($course->cover_path) {
            $this->fileService->delete($course->cover_path);
        }

        foreach ($course->materials as $material) {
            if ($material->file_path) {
                $this->fileService->delete($material->file_path);
            }
        }

        $course->delete();

        return $course;
    }

    private function transformCourseData($course)
    {
        $course->cover_url = $this->fileService->url($course->cover_path);
        $course->member_count = CourseEnrollment::where('course_id', $course->course_id)->count();
        $course->material_count = CourseMaterial::where('course_id', $course->course_id)->count();
        $course->quiz_count = Quiz::where('course_id', $course->course_id)->count();

        return $course;
    }
}