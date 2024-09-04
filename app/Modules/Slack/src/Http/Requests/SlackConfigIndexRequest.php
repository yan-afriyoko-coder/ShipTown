<?php

namespace App\Modules\Slack\src\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SlackConfigIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin');
    }
}
