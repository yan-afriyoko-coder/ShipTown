<?php

namespace App\Http\Requests\OrderStatus;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'name'              => ['required', 'string', 'max:100', 'unique:order_statuses,name'],
            'code'              => ['required', 'string', 'max:100', 'unique:order_statuses,code'],
            'order_active'      => ['required', 'boolean'],
            'reserves_stock'    => ['required', 'boolean'],
        ];
    }
}
