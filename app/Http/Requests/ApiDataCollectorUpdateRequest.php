<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiDataCollectorUpdateRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'action' => [
                'sometimes',
                'string',
                'in:transfer_in_scanned,transfer_out_scanned,' .
                'auto_scan_all_requested,transfer_between_warehouses,import_as_stocktake'
            ],
        ];
    }
}
