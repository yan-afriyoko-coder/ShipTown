<?php

namespace App\Http\Requests\QuantityDiscount;

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
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:250',
            'job_class' => ['nullable', 'sometimes', 'string', 'in:App\\Modules\\QuantityDiscounts\\src\\Jobs\\CalculateSoldPriceForBuyXForYPercentDiscount,App\\Modules\\QuantityDiscounts\\src\\Jobs\\CalculateSoldPriceForBuyXForYPriceDiscount,App\\Modules\\QuantityDiscounts\\src\\Jobs\\CalculateSoldPriceForBuyXGetYForZPercentDiscount,App\\Modules\\QuantityDiscounts\\src\\Jobs\\CalculateSoldPriceForBuyXGetYForZPriceDiscount'],
            'configuration' => 'required|array',
        ];
    }
}
