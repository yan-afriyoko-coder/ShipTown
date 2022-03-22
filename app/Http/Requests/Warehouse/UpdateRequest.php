<?php

namespace App\Http\Requests\Warehouse;

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
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'  => 'sometimes|string|max:250',
            'code'  => ['sometimes','string','max:5','unique:warehouses,code,'.$this->warehouse->id],
            'tags'  => 'sometimes|array',
        ];
    }
}
