<?php

namespace App\Http\Requests\Quiz;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreQuizRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        if ($this->has('questions')) {
            $questions = $this->input('questions');
            foreach ($questions as &$q) {
                // Hanya normalize options kalau tipe soal memang butuh options
                if (in_array($q['type'] ?? null, ['multiple_choice', 'true_false'])) {
                    if (isset($q['options'])) {
                        foreach ($q['options'] as &$opt) {
                            $opt['is_correct'] = isset($opt['is_correct']) ? 1 : 0;
                        }
                    }
                } else {
                    // Pastikan short_answer tidak punya options
                    unset($q['options']);
                }
            }
            $this->merge(['questions' => $questions]);
        }
    }

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'time_limit' => 'nullable|integer|min:1|max:480',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',

            'questions' => 'required|array|min:1',
            'questions.*.question_text' => 'required|string',
            'questions.*.type' => 'required|in:multiple_choice,true_false,short_answer',

            // hanya untuk multiple_choice dan true_false
            'questions.*.options' => 'required_if:questions.*.type,multiple_choice,true_false|array',
            'questions.*.options.*.option_text' => 'required_with:questions.*.options|string',
            'questions.*.options.*.is_correct' => 'required_with:questions.*.options|boolean',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Quiz title is required',
            'time_limit.integer' => 'Time limit must be a number in minutes',

            'questions.required' => 'At least one question is required',
            'questions.*.question_text.required' => 'Each question must have a text',
            'questions.*.type.in' => 'Question type must be multiple choice, true/false, or short answer',

            'questions.*.options.required_if' => 'Options are required for multiple choice and true/false questions',
            'questions.*.options.*.option_text.required_with' => 'Each option must have text',
            'questions.*.options.*.is_correct.required_with' => 'Each option must define whether it is correct or not',
            'questions.*.options.*.is_correct.boolean' => 'Correct answer flag must be true or false',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->all();

        foreach ($errors as $error) {
            flash($error)->error();
        }

        throw new HttpResponseException(redirect()->back()->withInput());
    }
}
