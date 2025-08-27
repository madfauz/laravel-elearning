<?php

namespace App\Observers;

use App\Models\Course;
use App\Services\FileStorageService;
use Illuminate\Support\Facades\Storage;

class CourseObserver
{
    protected $fileService;

    public function __construct(FileStorageService $fileService)
    {
        $this->fileService = $fileService;
    }
    /**
     * Handle the Course "created" event.
     *
     * @param  \App\Models\Course  $course
     * @return void
     */
    public function created(Course $course)
    {
        //
    }

    /**
     * Handle the Course "updated" event.
     *
     * @param  \App\Models\Course  $course
     * @return void
     */
    public function updated(Course $course)
    {
        //
    }

    /**
     * Handle the Course "deleted" event.
     *
     * @param  \App\Models\Course  $course
     * @return void
     */
    public function deleted(Course $course)
    {
        //
    }

    /**
     * Handle the Course "restored" event.
     *
     * @param  \App\Models\Course  $course
     * @return void
     */
    public function restored(Course $course)
    {
        //
    }

    /**
     * Handle the Course "force deleted" event.
     *
     * @param  \App\Models\Course  $course
     * @return void
     */
    public function forceDeleted(Course $course)
    {
        //
    }

    public function deleting(Course $course)
    {
        if ($course->cover_path) {
            $this->fileService->delete($course->cover_path);
        }

        foreach ($course->materials as $material) {
            if ($material->file_path) {
                $this->fileService->delete($material->file_path);
            }
        }
    }

}
