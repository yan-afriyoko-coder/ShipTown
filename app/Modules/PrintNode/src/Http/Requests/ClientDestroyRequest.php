<?php

namespace App\Modules\PrintNode\src\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientDestroyRequest extends FormRequest
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
