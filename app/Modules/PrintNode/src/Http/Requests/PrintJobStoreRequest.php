<?php

namespace App\Modules\PrintNode\src\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PrintJobStoreRequest extends FormRequest
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
            'printer_id' => ['int', 'required'],
            'pdf_url'    => ['sometimes'],
        ];
    }
}
