<?php

namespace App\Http\Requests\Budget;

use App\Rules\OperatorRule;
use Illuminate\Foundation\Http\FormRequest;

class SearchBudgetRequest extends FormRequest
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

            'filters.amount' => [
                'nullable',
                new OperatorRule(
                    allowedOperators :['eq', 'gte', 'lte', 'between'],
                    rules: ['numeric']
                ),
            ],

            'filters.start_date' => [
                'nullable',
                new OperatorRule(
                    allowedOperators: ['eq', 'gte', 'lte', 'between'],
                    rules: ['date']
                ),
            ],

            'filters.end_date' => [
                'nullable',
                new OperatorRule(
                    allowedOperators: ['eq', 'gte', 'lte', 'between'],
                    rules: ['date']
                ),
            ],

            'filters.icon' => [
                'nullable',
                new OperatorRule(
                    allowedOperators: ['eq', 'like'],
                    rules: ['string', 'max:255']
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
