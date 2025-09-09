<?php

namespace App\Http\Requests\Transaction;

use App\Rules\OperatorRule;
use Illuminate\Foundation\Http\FormRequest;

class SearchTransactionRequest extends FormRequest
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
            'filters' => ['nullable', 'array'],

            'filters.name' => [
                'nullable',
                new OperatorRule(
                    allowedOperators: ['eq', 'like'],
                    rules: ['string', 'max:255']
                ),
            ],

            'filters.description' => [
                'nullable',
                new OperatorRule(
                    allowedOperators: ['eq', 'like'],
                    rules: ['string', 'max:255']
                ),
            ],

            'filters.date' => [
                'nullable',
                new OperatorRule(
                    allowedOperators: ['eq', 'gte', 'lte', 'between'],
                    rules: ['date']
                ),
            ],

            'filters.is_recurring' => [
                'nullable',
                new OperatorRule(
                    allowedOperators: ['eq'],
                    rules: ['boolean']
                ),
            ],

            'filters.recurring_interval' => [
                'nullable',
                new OperatorRule(
                    allowedOperators: ['eq', 'gt', 'gte', 'lt', 'lte'],
                    rules: ['numeric']
                ),
            ],

            'filters.recurring_unit' => [
                'nullable',
                new OperatorRule(
                    allowedOperators: ['eq', 'in'],
                    rules: ['string', 'max:255', 'in:day,week,month,year']
                ),
            ],

            'filters.location' => [
                'nullable',
                new OperatorRule(
                    allowedOperators: ['eq', 'like'],
                    rules: ['string', 'max:255']
                ),
            ],

            'filters.amount' => [
                'nullable',
                new OperatorRule(
                    allowedOperators :['eq', 'gte', 'lte', 'between'],
                    rules: ['numeric']
                ),
            ],

            'filters.type' => [
                'nullable',
                new OperatorRule(
                    allowedOperators: ['eq', 'in'],
                    rules: ['string', 'max:255', 'in:income,expense']
                ),
            ],

            'filters.account_id' => [
                'nullable',
                new OperatorRule(
                    allowedOperators: ['eq'],
                    rules: ['string', 'uuid', 'exists:accounts,id']
                ),
            ],

            'filters.user_id' => [
                'nullable',
                new OperatorRule(
                    allowedOperators: ['eq'],
                    rules: ['string', 'uuid', 'exists:users,id']
                ),
            ],

            'filters.budget_id' => [
                'nullable',
                new OperatorRule(
                    allowedOperators: ['eq'],
                    rules: ['string', 'uuid', 'exists:budgets,id']
                ),
            ],

            // Sorting
            'sort' => ['nullable', 'string', 'max:255'],
            'direction' => ['nullable', 'string', 'in:asc,desc'],

            // Pagination (later if needed)
            // 'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            // 'paginate' => ['nullable', 'boolean'],
        ];
    }
}
