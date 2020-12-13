<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Allow if same user or has permissions.
        return $this->user->id == $this->user()->id || $this->user()->can('manage users');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255', 'unique:users,name,'.$this->user->id],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', 'unique:users,name,'.$this->user->id],
            'role_id' => ['sometimes', 'exists:roles,id'],
            'printer_id' => ['sometimes', 'numeric'],
        ];
    }
}
