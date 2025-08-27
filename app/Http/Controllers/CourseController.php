<?php

namespace App\Http\Controllers;

use App\Services\CourseService;
use App\Services\FileStorageService;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    protected $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    public function index(Request $request)
    {
        $courses = $this->courseService->getAllCourses($request);

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
            'description' => 'nullable|string',
            'type' => 'required|in:public,private',
            'access_code' => 'nullable|string|max:255',
            'cover_path' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('cover_path')) {
            $validated['cover_file'] = $request->file('cover_path');
        }

        $this->courseService->createCourse($validated);

        flash('Course created successfully')->success();
        return redirect()->route('manage-course.index');
    }

    public function show($course_id)
    {
        $course = $this->courseService->getCourseById($course_id);

        return view('course.show', compact('course'));
    }


    public function edit($course_id)
    {
        $course = $this->courseService->getCourseForEdit($course_id);

        return view('course.edit', compact('course'));
    }

    public function update(Request $request, $course_id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:public,private',
            'access_code' => 'nullable|string|max:255',
            'cover_path' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('cover_path')) {
            $validated['cover_file'] = $request->file('cover_path');
        }

        $this->courseService->updateCourse($course_id, $validated);

        flash('Course updated successfully')->success();

        return redirect()->route('manage-course.index');
    }

    public function destroy($course_id)
    {
        $this->courseService->deleteCourse($course_id);

        flash('Course deleted successfully')->success();
        return redirect()->route('manage-course.index');
    }
}