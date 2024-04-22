<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePdfPrintRequest extends FormRequest
{
    public function authorize():bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'data' => 'required',
            'data.labels' => 'required',
            'template' => 'required',
            'printer_id' => 'required | integer',
        ];
    }
}
