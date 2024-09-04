<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserMeStoreRequest extends FormRequest
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
            'name' => ['sometimes', 'string'],
            'printer_id' => ['sometimes', 'numeric'],
            'address_label_template' => ['sometimes', 'nullable', 'string', 'exists:"shipping_services",code'],
            'ask_for_shipping_number' => ['sometimes', 'boolean'],
        ];
    }
}
