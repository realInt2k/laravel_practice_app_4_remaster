<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class EmailDeha implements ValidationRule
{
    const ACCEPT_EMAILS = ['deha-soft.com', 'gmail.com'];
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $email = explode('@', $value);
        $domain = $email[array_key_last($email)];
        if (!in_array($domain, self::ACCEPT_EMAILS)) {
            $fail('email must be (deha-soft.com) or (gmail.com)');
        }
    }
}
