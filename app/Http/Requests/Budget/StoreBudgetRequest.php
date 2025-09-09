<?php

namespace App\Http\Requests\Budget;

use App\Models\Budget;
use Illuminate\Foundation\Http\FormRequest;

class StoreBudgetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Budget::class);
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
            'amount' => ['required', 'numeric'],
            'start_date' => ['required', 'date'],// TODO: Replace with recurring_unit/interval ??
            'end_date' => ['required', 'date'],
            'icon' => ['required', 'string', 'max:255'],
            'user_id' => ['required', 'string', 'uuid', 'exists:users,id'],
        ];
    }
}
