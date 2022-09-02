<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 *
 */
class DataCollectorStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
                'data_collection_id' => ['required', 'exists:data_collections,id'],
                'product_sku' => ['required_if:product_id,null', 'exists:products_aliases,alias'],
                'product_id' => ['required_if:product_sku,null', 'exists:products,id'],
                'quantity_requested' => ['sometimes', 'numeric'],
                'quantity_scanned' => ['sometimes', 'numeric'],
        ];
    }
}
