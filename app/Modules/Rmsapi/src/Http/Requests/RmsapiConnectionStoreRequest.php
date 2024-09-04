<?php

namespace App\Modules\Rmsapi\src\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RmsapiConnectionStoreRequest extends FormRequest
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
            'location_id' => ['required_if:id,null'],
            'url' => 'required_if:id,null|url',
            'username' => 'required_if:id,null',
            'password' => 'required_if:id,null',
            'warehouse_code' => 'sometimes|max:5|exists:warehouse,code',
        ];
    }
}
