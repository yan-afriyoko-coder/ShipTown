<?php

namespace App\Modules\Slack\src\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IncomingWebhookStoreRequest extends FormRequest
{
    public function authorize():bool
    {
        return $this->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'webhook_url' => 'required|url',
        ];
    }
}
