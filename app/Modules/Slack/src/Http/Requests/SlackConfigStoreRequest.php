<?php

namespace App\Modules\Slack\src\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SlackConfigStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'incoming_webhook_url' => 'nullable|url',
        ];
    }
}
