<?php

namespace App\Http\Requests\Transaction;

use App\Models\Transaction;
use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Transaction::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'is_recurring' => ['required', 'boolean'],
            'recurring_interval' => ['nullable', 'numeric'],
            'recurring_unit' => ['nullable', 'string', 'max:255', 'in:day,week,month,year'],
            'location' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'numeric'],
            'type' => ['required', 'string', 'max:255', 'in:income,expense'],
            'account_id' => ['required', 'string', 'uuid', 'exists:accounts,id'],
            'user_id' => ['required', 'string', 'uuid', 'exists:users,id'],
            'budget_id' => ['required', 'string', 'uuid', 'exists:budgets,id'],
        ];
    }
}
