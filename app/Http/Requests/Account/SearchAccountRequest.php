<?php

namespace App\Http\Requests\Account;

use App\Rules\OperatorRule;
use Illuminate\Foundation\Http\FormRequest;

class SearchAccountRequest extends FormRequest
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

            'filters.name' => [
                'nullable',
                new OperatorRule(
                    allowedOperators: ['eq', 'like'],
                    rules: ['string', 'max:255']
                ),
            ],

            'filters.type' => [
                'nullable',
                new OperatorRule(
                    allowedOperators: ['eq', 'in'],
                    rules: ['string', 'max:255', 'in:checking,savings,credits,investment']
                ),
            ],

            'filters.balance' => [
                'nullable',
                new OperatorRule(
                    allowedOperators: ['eq', 'gt', 'gte', 'lt', 'lte'],
                    rules: ['numeric']
                ),
            ],

            'filters.icon' => [
                'nullable',
                new OperatorRule(
                    allowedOperators: ['eq', 'like'],
                    rules: ['string', 'max:255']
                ),
            ],

            'filters.currency_id' => [
                'nullable',
                new OperatorRule(
                    allowedOperators: ['eq'],
                    rules: ['string', 'uuid', 'exists:currencies,id']
                ),
            ],

            'filters.user_id' => [
                'nullable',
                new OperatorRule(
                    allowedOperators: ['eq'],
                    rules: ['string', 'uuid', 'exists:users,id']
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
