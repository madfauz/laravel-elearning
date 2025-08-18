<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseMaterial;
use App\Models\User;
use App\Services\FileStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CourseController extends Controller
{

    protected $fileService;

    public function __construct(FileStorageService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function index(Request $request)
    {
        $query = Course::query()->with("teacher")->orderByDesc("updated_at");

        if ($request->routeIs('manage-course.index')) {
            $query->where('teacher_id', auth()->id());
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $type = $request->type;
            $query->where('type', $type);
        }

        $courses = $query->paginate(10);

        $courses->getCollection()->transform(function ($course) {
            $course->cover_url = $this->fileService->url($course->cover_path);
            $course->member_count = CourseEnrollment::where('course_id', $course->course_id)->count();
            $course->material_count = CourseMaterial::where('course_id', $course->course_id)->count();
            $course->owner = User::where('user_id', $course->teacher_id)->first();
            return $course;
        });

        if ($request->routeIs('manage-course.index')) {
            return view('manage-course.index', compact('courses'));
        } else {
            return view('main.dashboard', compact('courses'));
        }
    }


    public function create()
    {
        return view('course.create');
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:public,private',
            'access_code' => 'nullable|string|max:255',
            'cover_path' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $newStore = [
            'course_id' => uuid_create(),
            'teacher_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
        ];

        if ($validated['type'] === 'private' && empty($validated['access_code'])) {
            $newStore['access_code'] = Str::upper(Str::random(8)); // contoh kode random
        }

        if ($request->hasFile('cover_path')) {
            $newStore['cover_path'] = $this->fileService->upload(
                $request->file('cover_path'),
                'courses/covers'
            );
        }

        $course = Course::create($newStore);

        flash('Course created successfully')->success();
        return redirect()
            ->route('manage-course.index');
    }

    public function show($course_id, FileStorageService $fileService)
    {
        $course = Course::with('teacher')
            ->where('course_id', $course_id)
            ->firstOrFail();

        $course->cover_url = $this->fileService->url($course->cover_path);

        foreach ($course->materials as $material) {
            $material->file_path = $this->fileService->url($material->file_path);
        }

        return view('course.show', compact('course'));
    }

    public function edit($course_id, FileStorageService $fileService)
    {
        $course = Course::with('teacher')->where('course_id', $course_id)->firstOrFail();
        $course->cover_url = $this->fileService->url($course->cover_path);

        return view('course.edit', compact('course', 'fileService'));
    }

    public function update(Request $request, $course_id)
    {
        $course = Course::findOrFail($course_id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:public,private',
            'access_code' => 'nullable|string|max:255',
            'cover_path' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $updateData = [
            'title' => $validated['title'],
            'description' => $validated['description'],
            'type' => $validated['type'],
        ];

        if ($validated['type'] === 'private') {
            $updateData['access_code'] = $validated['access_code'] ?: Str::upper(Str::random(8));
        } else {
            $updateData['access_code'] = null;
        }

        if ($request->hasFile('cover_path')) {
            if ($course->cover_path) {
                $this->fileService->delete($course->cover_path);
            }

            $updateData['cover_path'] = $this->fileService->upload(
                $request->file('cover_path'),
                'courses/covers'
            );
        }

        $course->update($updateData);

        flash('Course updated successfully')->success();

        return redirect()->route('manage-course.index');
    }

    public function destroy($id)
    {
        $course = Course::findOrFail($id);

        if ($course->cover_path) {
            $this->fileService->delete($course->cover_path);
        }

        $course->delete();

        flash('Course deleted successfully')->success();
        return redirect()->route('manage-course.index');
    }
}
