<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailCommand extends Command
{
    protected $signature = 'email:test {email}';
    protected $description = 'Test email configuration';

    public function handle()
    {
        $email = $this->argument('email');

        try {
            Mail::raw('Testovací email z Sports Club aplikace. Emailový systém funguje správně!', function ($message) use ($email) {
                $message->to($email)
                        ->subject('Test emailu - Sports Club');
            });

            $this->info('✅ Testovací email úspěšně odeslán na: ' . $email);
        } catch (\Exception $e) {
            $this->error('❌ Chyba při odesílání emailu: ' . $e->getMessage());
        }
    }
}
