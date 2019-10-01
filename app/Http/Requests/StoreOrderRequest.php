<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'order_number' => 'required',
            'products' => 'required|array',
            'products.*.sku' => 'required',
            'products.*.quantity' => 'required|numeric',
            'products.*.price' => 'required|numeric',
        ];
    }
}
