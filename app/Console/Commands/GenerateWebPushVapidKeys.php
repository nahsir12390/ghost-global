<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Minishlink\WebPush\VAPID;

class GenerateWebPushVapidKeys extends Command
{
    protected $signature = 'push:generate-vapid-keys';

    protected $description = 'Generate the VAPID keys required for browser push notifications';

    public function handle(): int
    {
        $keys = VAPID::createVapidKeys();

        $this->newLine();
        $this->line('Add these values to your .env file. Keep the private key secret.');
        $this->newLine();
        $this->line('WEB_PUSH_VAPID_SUBJECT=mailto:'.config('mail.from.address', 'you@example.com'));
        $this->line('WEB_PUSH_VAPID_PUBLIC_KEY='.$keys['publicKey']);
        $this->line('WEB_PUSH_VAPID_PRIVATE_KEY='.$keys['privateKey']);
        $this->newLine();
        $this->line('Then run: php artisan config:clear');

        return self::SUCCESS;
    }
}
