<?php

namespace App\Http\Requests\Automations;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
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
     * Get the condition rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $config = collect(config('automations.when'));
        $availableEvent = $config->pluck('class');
        $availableValidation = $config->where('class', $this->event_class)
                            ->pluck('conditions')
                            ->collapse()->pluck('class')
                            ->unique();
        $availableActions = $config->where('class', $this->event_class)
                            ->pluck('actions')
                            ->collapse()->pluck('class')
                            ->unique();
        return [
            'name' => 'required|min:3|max:200',
            'event_class' => [
                'nullable',
                Rule::in($availableEvent),
            ],
            'enabled' => 'required|boolean',
            'priority' => 'required|numeric',

            // Conditions
            'conditions.*.condition_class' => [
                'nullable',
                Rule::in($availableValidation),
            ],
            'conditions.*.condition_value' => 'nullable|string',

            // Actions
            'actions.*.action_class' => [
                'nullable',
                Rule::in($availableActions),
            ],
            'actions.*.action_value' => 'nullable|string'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'conditions.*.condition_class' => 'condition',
            'conditions.*.condition_value' => 'condition value',
            'actions.*.action_class'  => 'action value',
            'actions.*.action_value'  => 'action value',
        ];
    }
}
