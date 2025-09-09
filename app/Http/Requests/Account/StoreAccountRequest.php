<?php

namespace App\Http\Requests\Account;

use App\Models\Account;
use Illuminate\Foundation\Http\FormRequest;

class StoreAccountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Account::class);
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
            'type' => ['required', 'string', 'max:255', 'in:checking,savings,credits,investment'],
            'balance' => ['nullable', 'numeric'],
            'icon' => ['required', 'string', 'max:255'],
            'currency_id' => ['required', 'string', 'uuid', 'exists:currencies,id'],
            'user_id' => ['required', 'string', 'uuid', 'exists:users,id'],
        ];
    }
}
