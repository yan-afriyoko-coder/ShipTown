<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AutoStatusConfigurationStoreRequest extends FormRequest
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
            'max_batch_size' => 'sometimes|integer',
            'max_order_age' => 'sometimes|integer',
        ];
    }
}
