<?php

namespace App\Http\Middleware;

use App\Models\Course;
use App\Models\CourseEnrollment;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CheckCourseAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $course_id = $request->route('course_id');
        $course = Course::findOrFail($course_id);
        $user_id = auth()->id();

        if ($course->teacher_id === $user_id) {
            return $next($request);
        }

        $isEnrolled = CourseEnrollment::where('course_id', $course->course_id)
            ->where('student_id', $user_id)
            ->exists();

        if ($course->type === 'public') {
            if (!$isEnrolled) {
                CourseEnrollment::create([
                    'course_enrollment_id' => Str::uuid(),
                    'course_id' => $course->course_id,
                    'student_id' => $user_id,
                    'joined_at' => now(),
                ]);
            }
            return $next($request);
        }

        if ($isEnrolled) {
            return $next($request);
        }

        flash('You must enter the access code to join this private course.')->error();
        return redirect()->route('course.access-code', $course->course_id);
    }
}
