<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;

class OperatorRule implements ValidationRule
{
    public function __construct(
        private readonly array $allowedOperators,
        private readonly array $rules,
    ) {}

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_array($value)) {
            $fail("$attribute must be an array of operators.");
            return;
        }

        foreach ($value as $operator => $operand) {
            if (!in_array($operator, $this->allowedOperators)) {
                $fail("The operator '$operator' is not allowed for $attribute.");
            }

            if ($operator === 'between') {
                if (!is_array($operand) || count($operand) !== 2) {
                    $fail("$attribute.$operator must be an array with exactly 2 values.");
                    continue;
                }
                // Validate each values
                foreach ($operand as $index => $betweenValue) {
                    $this->validateRules($attribute, $operator, $betweenValue, $fail, $index);
                }
                continue;
            }

            if ($operator === 'in') {
                if (!is_array($operand)) {
                    $fail("$attribute.$operator must be an array.");
                    continue;
                }
                // Validate each values
                foreach ($operand as $index => $inValue) {
                    $this->validateRules($attribute, $operator, $inValue, $fail, $index);
                }
                continue;
            }

            $this->validateRules($attribute, $operator, $operand, $fail);
        }
    }

    private function validateRules(string $attribute, string $operator, mixed $value, Closure $fail, ?int $index = null): void
    {
        $validator = Validator::make(['value' => $value], ['value' => $this->rules]);
        if ($validator->fails()) {
            foreach ($validator->errors()->get('value') as $error) {
                $suffix = $index !== null ? "[$index]" : "";
                $fail("$attribute.$operator$suffix: $error");
            }
        }
    }
}
