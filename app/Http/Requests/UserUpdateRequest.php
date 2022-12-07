<?php

namespace App\Http\Requests;

use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class UserUpdateRequest
 * @package App\Http\Request
 *
 * @mixin User
 */
class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin', 'api');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $updatedUserId = $this->route('user');

        return [
            'name' => [
                'string',
                'sometimes',
                'required',
                'max:255',
                Rule::unique('users', 'name')->ignore($updatedUserId),
            ],

            'email' => [
                'email',
                'string',
                'sometimes',
                'required',
                'max:255',
                Rule::unique('users', 'email')->ignore($updatedUserId),
            ],

            'default_dashboard_uri' => [
                'nullable',
                'string',
                'sometimes',
                'max:255'
            ],

            'role_id' => Rule::when($updatedUserId !== $this->user()->id, [
                'required',
                'integer',
                Rule::exists('roles', 'id')->where('guard_name', 'api'),
            ]),

            'printer_id' => [
                'sometimes',
                'numeric'
            ],

            'warehouse_id' => [
                'nullable',
                'exists:warehouses,id'
            ],
        ];
    }
}
