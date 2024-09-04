<?php

namespace App\Http\Requests\MailTemplate;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'subject' => ['required', 'string', 'max:300'],
            'html_template' => ['required', 'string'],
            'text_template' => ['nullable', 'string'],
            'reply_to' => ['nullable', 'email'],
            'to.*' => ['email'],
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $email = str_replace(' ', ',', $this->to);
        $email = str_replace(',,', ',', $email);
        $email = collect(explode(',', $email))->filter(function ($email) {
            return $email != '';
        })->toArray();

        $this->merge([
            'to' => $email,
        ]);
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->has('to.*')) {
                $validator->errors()->add('to', 'The to must be a valid email address.');
            }
        });
    }
}
