<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShipmentStoreRequestNew extends FormRequest
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
            'order_id' => ['required', 'exists:orders,id'],
            'shipping_number' => ['required', 'string'],
            'carrier' => ['sometimes', 'string'],
            'service' => ['sometimes', 'string'],
        ];
    }
}
