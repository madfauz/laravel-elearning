<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourseEnrollmentController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }

    public function showForm($course_id)
    {
        $course = Course::findOrFail($course_id);

        return view('course.access-code', compact('course'));
    }

    public function verifyCode(Request $request, $course_id)
    {
        $course = Course::findOrFail($course_id);

        $request->validate([
            'access_code' => 'required|string',
        ]);

        if ($request->access_code === $course->access_code) {
            CourseEnrollment::firstOrCreate([
                'course_enrollment_id' => Str::uuid(),
                'course_id' => $course->course_id,
                'student_id' => auth()->id(),
            ]);

            flash('Access granted successfully!')->success();
            return redirect()->route('course.show', $course->course_id);
        }

        flash('Access code is invalid')->error();
        return back()->withInput();
    }

    public function leaveCourse($course_id)
    {
        $enrollment = CourseEnrollment::where('course_id', $course_id)
            ->where('student_id', auth()->id())
            ->first();

        $enrollment->delete();

        flash('You have left the course successfully')->success();
        return redirect()->route('main.dashboard');
    }
}
