<?php

namespace App\Http\Requests\Transaction;

use App\Models\Transaction;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', Transaction::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'date' => ['nullable', 'date'],
            'is_recurring' => ['nullable', 'boolean'],
            'recurring_interval' => ['nullable', 'numeric'],
            'recurring_unit' => ['nullable', 'string', 'max:255', 'in:day,week,month,year'],
            'location' => ['nullable', 'string', 'max:255'],
            'amount' => ['nullable', 'numeric'],
            'type' => ['nullable', 'string', 'max:255', 'in:income,expense'],
            'account_id' => ['prohibited'],
            'user_id' => ['prohibited'],
            'budget_id' => ['prohibited'],
        ];
    }
}
