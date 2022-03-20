<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConfigurationRmsApiRequest extends FormRequest
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
            'location_id'    => 'required',
            'url'            => 'required|url',
            'username'       => 'required',
            'password'       => 'required',
            'warehouse_code' => 'sometimes|max:5|exist2s:warehouse,code',
        ];
    }
}
