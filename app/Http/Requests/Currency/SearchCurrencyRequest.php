<?php

namespace App\Http\Requests\Currency;

use App\Rules\OperatorRule;
use Illuminate\Foundation\Http\FormRequest;

class SearchCurrencyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;// Gate are check in the SearchService
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Filters
            'filters' => ['nullable', 'array'],

            'filters.code' => [
                'nullable',
                new OperatorRule(
                    allowedOperators: ['eq', 'like'],
                    rules: ['string', 'max:255']
                ),
            ],

            'filters.name' => [
                'nullable',
                new OperatorRule(
                    allowedOperators: ['eq', 'like'],
                    rules: ['string', 'max:255']
                ),
            ],

            'filters.symbol' => [
                'nullable',
                new OperatorRule(
                    allowedOperators: ['eq', 'like'],
                    rules: ['string', 'max:255']
                ),
            ],

            // Sort
            'sort' => ['nullable', 'string', 'max:255'],
            'direction' => ['nullable', 'string', 'in:asc,desc'],

            // TODO: Pagination
            //'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            //'paginate' => ['nullable', 'boolean'],
        ];
    }
}
