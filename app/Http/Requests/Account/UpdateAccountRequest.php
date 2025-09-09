<?php

namespace App\Http\Requests\Account;

use App\Models\Account;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAccountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', Account::class);
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
            'type' => ['nullable, string', 'max:255', 'in:checking,savings,credits,investment'],
            'balance' => ['nullable', 'numeric'],
            'icon' => ['nullable', 'string', 'max:255'],
            'currency_id' => ['prohibited'],
            'user_id' => ['prohibited'],
        ];
    }
}
