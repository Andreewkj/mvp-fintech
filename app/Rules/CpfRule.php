<?php

declare(strict_types=1);

namespace App\Rules;

use App\Domain\Entities\Cpf;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CpfRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            (new Cpf($value));
        } catch (\InvalidArgumentException $e) {
            $fail($e->getMessage());
        }
    }
}
