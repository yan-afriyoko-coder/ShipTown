<?php

namespace App\Http\Requests\Picklist;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeletedPickRequest extends FormRequest
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
            'quantity_picked'          => 'numeric|required_without:quantity_skipped_picking',
            'quantity_skipped_picking' => 'numeric|required_without:quantity_picked',
            'order_product_ids'        => 'required|array',
            'order_product_ids.*'      => 'numeric|exists:order_products,id',
        ];
    }
}
