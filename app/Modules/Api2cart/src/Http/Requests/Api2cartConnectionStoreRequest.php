<?php

namespace App\Modules\Api2cart\src\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Api2cartConnectionStoreRequest extends FormRequest
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
            'location_id' => 'required',
            'url' => 'required|url',
            'type' => 'required',
            'bridge_api_key' => 'required',
        ];
    }
}
