<?php

namespace App\Http\Requests\Automations;

use App\Modules\Automations\src\Services\AutomationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the condition rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $available_event_classes = AutomationService::availableEvents()->pluck('class');
        $available_conditions_classes = AutomationService::availableConditions()->pluck('class');
        $available_action_classes = AutomationService::availableActions()->pluck('class');

        return [
            'name' => 'required|min:3|max:200',
            'description' => 'nullable|string',
            'event_class' => ['nullable', Rule::in($available_event_classes)],
            'enabled' => 'required|boolean',
            'priority' => 'required|numeric',
            'conditions.*.condition_class' => ['nullable', Rule::in($available_conditions_classes)],
            'conditions.*.condition_value' => 'nullable|string',
            'actions.*.action_class' => ['nullable', Rule::in($available_action_classes)],
            'actions.*.action_value' => 'nullable|string'
        ];
    }
}
