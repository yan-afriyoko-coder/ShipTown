<?php

namespace App\Http\Requests\NavigationMenu;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|min:3|max:100',
            'url' => 'required|string|min:3|max:999',
            'group' => 'required|min:3|max:100|in:picklist,packlist,reports',
        ];
    }
}
