<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseMaterial;
use App\Services\FileStorageService;
use Illuminate\Http\Request;

class CourseMaterialController extends Controller
{

    protected $fileService;
    public function __construct(FileStorageService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function index(Request $request, $course_id)
    {
        $query = CourseMaterial::query()->with('course')->where('course_id', $course_id)->orderByDesc('updated_at');
        $course = Course::where('course_id', $course_id)->first();

        if (request()->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $course_materials = $query->paginate(10);

        $course_materials->getCollection()->transform(function ($course_material) {
            $course_material->cover_url = $this->fileService->url($course_material->file_path);
            return $course_material;
        });

        return view('manage-material.index', compact('course_materials', 'course'));
    }

    public function create($course_id)
    {
        $course = Course::findOrFail($course_id);
        return view('manage-material.create', compact('course'));
    }

    public function store(Request $request, $course_id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content_text' => 'nullable|string',
            'file_path' => 'nullable|file|max:20480',
        ]);

        $newMaterial = [
            'course_material_id' => uuid_create(),
            'course_id' => $course_id,
            'title' => $request->title,
            'description' => $request->description,
            'content_text' => $request->content_text,
        ];

        if ($request->hasFile('file_path')) {
            $file = $request->file('file_path');
            $mimeType = $file->getMimeType();

            if (str_starts_with($mimeType, 'image/')) {
                $folder = 'course_materials/images';
            } elseif (str_starts_with($mimeType, 'video/')) {
                $folder = 'course_materials/videos';
            } else {
                $folder = 'course_materials/files';
            }

            $newMaterial['file_path'] = $this->fileService->upload($file, $folder);
        }

        CourseMaterial::create($newMaterial);

        flash('Material created successfully')->success();
        return redirect()->route('manage-material.index', ['course_id' => $course_id]);
    }


    public function show($id)
    {

    }
    public function edit($course_material_id)
    {
        $courseMaterial = CourseMaterial::findOrFail($course_material_id);

        return view('manage-material.edit', compact('courseMaterial'));
    }

    public function update(Request $request, $course_material_id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content_text' => 'nullable|string',
            'file_path' => 'nullable|file|max:20480',
        ]);

        $material = CourseMaterial::where('course_material_id', $course_material_id)
            ->firstOrFail();

        $material->title = $validated['title'];
        $material->description = $validated['description'] ?? null;
        $material->content_text = $validated['content_text'] ?? null;

        if ($request->hasFile('file_path')) {
            if ($material->file_path) {
                $this->fileService->delete($material->file_path);
            }

            $file = $request->file('file_path');
            $mimeType = $file->getMimeType();

            if (str_starts_with($mimeType, 'image/')) {
                $folder = 'course_materials/images';
            } elseif (str_starts_with($mimeType, 'video/')) {
                $folder = 'course_materials/videos';
            } else {
                $folder = 'course_materials/files';
            }

            $material->file_path = $this->fileService->upload($file, $folder);
        }

        $material->save();

        flash('Material updated successfully')->success();
        return redirect()->route('manage-material.index', ['course_id' => $material->course_id]);
    }

    public function destroy($course_material_id)
    {
        $material = CourseMaterial::findOrFail($course_material_id);

        if ($material->file_path) {
            $this->fileService->delete($material->file_path);
        }

        $material->delete();

        flash('Material deleted successfully')->success();
        return redirect()->route('manage-material.index', ['course_id' => $material->course_id]);
    }
}
