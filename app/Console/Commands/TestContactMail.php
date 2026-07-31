<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Helpers\SettingsHelper;

class TestContactMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:contact-mail {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test contact form email sending';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $testEmail = $this->argument('email') ?? 'test@example.com';
        
        $this->info('Testing contact form email...');
        $this->line('');
        $this->info('Mail Configuration:');
        $this->line('  Driver: ' . config('mail.default'));
        $this->line('  Host: ' . config('mail.mailers.smtp.host'));
        $this->line('  Port: ' . config('mail.mailers.smtp.port'));
        $this->line('  From: ' . config('mail.from.address'));
        $this->line('');
        
        try {
            $adminEmail = SettingsHelper::get('site_email', config('mail.from.address'));
            $this->info('Admin Email: ' . $adminEmail);
            
            // Send a test confirmation email
            Mail::to($testEmail)->send(
                new \App\Mail\ContactConfirmationMail(
                    name: 'Test User',
                    siteName: config('app.name')
                )
            );
            
            $this->info('✓ Test email sent successfully to: ' . $testEmail);
            
        } catch (\Exception $e) {
            $this->error('✗ Error sending test email:');
            $this->error($e->getMessage());
            $this->line('');
            $this->error('Stack trace:');
            $this->error($e->getTraceAsString());
            
            return 1;
        }
        
        return 0;
    }
}
