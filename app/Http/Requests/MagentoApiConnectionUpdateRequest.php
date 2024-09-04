<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MagentoApiConnectionUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'base_url' => 'required|url',
            'magento_store_id' => 'required|numeric',
            'tag' => 'required',
            'pricing_source_warehouse_id' => 'required|exists:warehouses,id',
            'access_token_encrypted' => 'required',
        ];
    }
}
