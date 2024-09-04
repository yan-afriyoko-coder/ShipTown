<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'shipping_number' => ['sometimes'],
            'label_template' => ['sometimes', 'exists:shipping_services,code', 'nullable'],
            'status_code' => ['sometimes'],
            'packed_at' => ['sometimes', 'date'],
            'packer_user_id' => ['sometimes', 'integer', 'exists:users,id'],
            'is_packed' => ['sometimes', 'boolean'],
        ];
    }
}
