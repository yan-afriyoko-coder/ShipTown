<?php

namespace App\Http\Requests\Warehouse;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property int $warehouse_id
 */
class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:250',
            'code' => ['sometimes', 'string', 'max:5'],
            'tags' => 'sometimes|array',
        ];
    }
}
