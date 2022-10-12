<?php

namespace App\Http\Requests;

use App\User;
use Illuminate\Foundation\Http\FormRequest;

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
            'name'                  => [
                'sometimes', 'required', 'string', 'max:255', 'unique:users,name,'.$this->user->id
            ],
            'email'                 => ['sometimes', 'required', 'string', 'email', 'max:255', 'unique:users,email,'.$this->user->id],
            'default_dashboard_uri' => ['sometimes', 'nullable', 'string', 'max:255'],
            'role_id'       => ['sometimes', 'exists:roles,id'],
            'printer_id'    => ['sometimes', 'numeric'],
            'warehouse_id'  => ['nullable', 'exists:warehouses,id'],
        ];
    }
}
