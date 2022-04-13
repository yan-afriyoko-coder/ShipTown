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
            'name'              => ['required', 'string', 'max:100'],
            'code'              => ['required', 'string', 'max:100'],
            'order_active'      => ['required', 'boolean'],
            'order_on_hold'     => ['required', 'boolean'],
            'reserves_stock'    => ['required', 'boolean'],
            'sync_ecommerce'    => ['required', 'boolean'],
        ];
    }
}
