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
            'job_class' => ['nullable', 'sometimes', 'string', 'in:App\\Modules\\DataCollectorQuantityDiscounts\\src\\Jobs\\CalculateSoldPriceForBuyXForYPercentDiscount,App\\Modules\\DataCollectorQuantityDiscounts\\src\\Jobs\\CalculateSoldPriceForBuyXForYPriceDiscount,App\\Modules\\DataCollectorQuantityDiscounts\\src\\Jobs\\CalculateSoldPriceForBuyXGetYForZPercentDiscount,App\\Modules\\DataCollectorQuantityDiscounts\\src\\Jobs\\CalculateSoldPriceForBuyXGetYForZPriceDiscount,App\\Modules\\DataCollectorQuantityDiscounts\\src\\Jobs\\CalculateSoldPriceForMultibuyPercentDiscount,App\\Modules\\DataCollectorQuantityDiscounts\\src\\Jobs\\CalculateSoldPriceForMultibuyPriceDiscount'],
            'configuration' => 'required|array',
        ];
    }
}
