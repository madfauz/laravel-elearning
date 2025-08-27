<?php

namespace App\Http\Requests\Quiz;

use Illuminate\Foundation\Http\FormRequest;

class SubmitQuizRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|uuid|exists:quiz_questions,question_id',
            'answers.*.option_id' => 'nullable|uuid|exists:quiz_options,option_id',
            'answers.*.answer_text' => 'nullable|string|max:1000',
        ];
    }
}