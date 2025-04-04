<?php

namespace App\Rules;

use App\Domain\Entities\Cnpj;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CnpjRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            (new Cnpj($value));
        } catch (\InvalidArgumentException $e) {
            $fail($e->getMessage());
        }
    }
}
