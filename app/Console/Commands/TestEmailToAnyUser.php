<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailToAnyUser extends Command
{
    protected $signature = 'test:email-any {email}';
    protected $description = 'Test sending email to any email address';

    public function handle()
    {
        $email = $this->argument('email');

        try {
            Mail::raw('This is a test email sent to any user: ' . $email, function ($message) use ($email) {
                $message->to($email)
                        ->subject('Test Email to Any User')
                        ->from(config('mail.from.address'), config('mail.from.name'));
            });

            $this->info('Test email sent successfully to ' . $email);
            $this->info('From: ' . config('mail.from.address'));
        } catch (\Exception $e) {
            $this->error('Failed to send email: ' . $e->getMessage());
        }
    }
}
