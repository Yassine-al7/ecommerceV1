<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Log;

class ValidEmailAddress implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // 1. Vérification de base de la syntaxe
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // 2. Vérifier que le domaine a des enregistrements MX
        $domain = substr(strrchr($value, "@"), 1);

        if (!$this->checkMXRecord($domain)) {
            return false;
        }

        // 3. Vérifier les domaines jetables/temporaires
        if ($this->isDisposableEmail($domain)) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The email address is invalid or cannot receive emails.';
    }

    /**
     * Vérifier les enregistrements MX du domaine
     */
    private function checkMXRecord(string $domain): bool
    {
        try {
            return checkdnsrr($domain, 'MX') || checkdnsrr($domain, 'A');
        } catch (\Exception $e) {
            Log::warning('MX record check failed for domain: ' . $domain . ' - ' . $e->getMessage());
            return true; // En cas d'erreur, on laisse passer pour éviter les faux positifs
        }
    }

    /**
     * Vérifier si c'est un email jetable
     */
    private function isDisposableEmail(string $domain): bool
    {
        $disposableDomains = [
            '10minutemail.com',
            'tempmail.org',
            'guerrillamail.com',
            'mailinator.com',
            'yopmail.com',
            'temp-mail.org',
            'throwaway.email',
            'maildrop.cc',
            'sharklasers.com',
            'grr.la',
            'guerrillamailblock.com',
            // Ajoutez d'autres domaines jetables selon vos besoins
        ];

        return in_array(strtolower($domain), $disposableDomains);
    }
}
