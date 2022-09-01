<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DataCollectorStoreRequest extends FormRequest
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
            'data_collection_id' => ['required', 'exists:data_collections,id'],
            'product_id' => ['required', 'exists:products,id'],
            'quantity_scanned' => ['required', 'numeric'],
        ];
    }
}
