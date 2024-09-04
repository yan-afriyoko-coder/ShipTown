<?php

namespace App\Modules\Api2cart\src\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Api2cartConnectionDestroyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            //
        ];
    }
}
