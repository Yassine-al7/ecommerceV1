<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Rules\ValidEmailAddress;

class TestEmailValidation extends Command
{
    protected $signature = 'test:email-validation {email}';
    protected $description = 'Test email validation rule';

    public function handle()
    {
        $email = $this->argument('email');
        $validator = new ValidEmailAddress();

        if ($validator->passes('email', $email)) {
            $this->info("✅ Email '{$email}' is valid and can receive emails.");
        } else {
            $this->error("❌ Email '{$email}' is invalid: " . $validator->message());
        }
    }
}
