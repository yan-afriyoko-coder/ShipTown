<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDpdIrelandRequest extends FormRequest
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
            'live' => 'required|boolean',
            'token' => 'required',
            'user' => 'required',
            'password' => 'required',
            'contact' => 'required',
            'contact_telephone' => 'required',
            'contact_email' => 'sometimes',
            'business_name' => 'sometimes',
            'address_line_1' => 'sometimes',
            'address_line_2' => 'sometimes',
            'address_line_3' => 'required',
            'address_line_4' => 'required',
            'country_code' => 'required|in:IE,IRL,UK,GB',
        ];
    }
}
