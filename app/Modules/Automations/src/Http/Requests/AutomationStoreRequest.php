<?php

namespace App\Modules\Automations\src\Http\Requests;

use App\Modules\Automations\src\Services\AutomationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AutomationStoreRequest extends FormRequest
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
     * Get the condition rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $available_conditions_classes = AutomationService::availableConditions()->pluck('class');
        $available_action_classes = AutomationService::availableActions()->pluck('class');

        return [
            'name' => 'required|min:3|max:200',
            'description' => 'nullable|string',
            'enabled' => 'required|boolean',
            'priority' => 'required|numeric',
            'conditions.*.condition_class' => ['nullable', Rule::in($available_conditions_classes)],
            'conditions.*.condition_value' => 'nullable|string',
            'actions.*.action_class' => ['nullable', Rule::in($available_action_classes)],
            'actions.*.action_value' => 'nullable|string',
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
            'actions.*.action_class' => 'action value',
            'actions.*.action_value' => 'action value',
        ];
    }
}
