<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobStoreRequest extends FormRequest
{
    public function authorize():bool
    {
        return $this->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'job_id' => 'required|numeric|exists:manual_request_jobs,id',
        ];
    }
}
