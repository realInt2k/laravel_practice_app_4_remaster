<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class EmailDeha implements ValidationRule
{
    const ACCEPT_EMAIL = 'deha-soft.com';
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $email = explode('@', $value);
        $domain = $email[array_key_last($email)];
        if ($domain !== self::ACCEPT_EMAIL) {
            $fail(':attribute must be email of DEHA (deha-soft.com)');
        }
    }
}
