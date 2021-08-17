<?php

namespace App\Http\Requests\OrderProductShipment;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'order_product_id'  => ['sometimes', 'integer', 'exists:order_products,id'],
            'quantity_shipped'  => ['sometimes', 'numeric', 'min:0'],
            'order_shipment_id' => ['sometimes', 'integer', 'exists:orders_products_shipments,id'],
        ];
    }
}
