<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $user = User::findOrFail($this->route('user_id'));

        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'max:255',
                'alpha_num',
                Rule::unique('users')->ignore($user->user_id, "user_id")
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->user_id, 'user_id')
            ],
            'role' => ['required', 'string', 'exists:roles,name'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Name is required',
            'username.required' => 'Username is required',
            'username.alpha_num' => 'Username must contain only letters and numbers',
            'username.unique' => 'Username is already taken',
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email address',
            'email.unique' => 'Email is already taken',
            'role.required' => 'Role is required',
            'role.exists' => 'Selected role is invalid',
        ];
    }
}