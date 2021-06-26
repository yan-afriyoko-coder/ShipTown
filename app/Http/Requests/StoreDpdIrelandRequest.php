<?php

namespace App\Http\Requests;

use App\Modules\DpdIreland\src\Models\DpdIreland;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
     * If no configuration found, token, user, and password field is required.
     *
     * @return array
     */
    public function rules()
    {
        $baseRule = [
            'user'              => 'required',
            'live'              => 'required|boolean',
            'contact'           => 'required',
            'contact_telephone' => 'required',
            'contact_email'     => 'sometimes',
            'business_name'     => 'sometimes',
            'address_line_1'    => 'sometimes',
            'address_line_2'    => 'sometimes',
            'address_line_3'    => 'required',
            'address_line_4'    => 'required',
            'country_code'      => 'required|in:IE,IRL,UK,GB',
        ];

        $config = DpdIreland::first();

        if (!$config) {
            return array_merge($baseRule, [
                'token'    => 'required',
                'password' => 'required',
            ]);
        }

        return array_merge($baseRule, [
            'token'    => ['sometimes', Rule::notIn(['*****', ''])],
            'password' => ['sometimes', Rule::notIn(['*****', ''])],
        ]);
    }
}
