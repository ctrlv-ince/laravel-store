<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SetupEmailConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up email configuration for the application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Setting up email configuration...');

        // Get current .env content
        $envPath = base_path('.env');
        $envContent = File::get($envPath);

        // Ask for email configuration
        $mailer = $this->choice('Select mail driver', ['smtp', 'mailtrap', 'mailgun', 'ses', 'postmark'], 0);
        
        if ($mailer === 'mailtrap') {
            $this->info('Using Mailtrap configuration...');
            $host = 'sandbox.smtp.mailtrap.io';
            $port = '2525';
            $username = $this->ask('Enter Mailtrap username');
            $password = $this->ask('Enter Mailtrap password');
            $encryption = 'tls';
        } else {
            $host = $this->ask('Enter SMTP host (leave empty for default)', 'smtp.mailgun.org');
            $port = $this->ask('Enter SMTP port (leave empty for default)', '587');
            $username = $this->ask('Enter SMTP username');
            $password = $this->ask('Enter SMTP password');
            $encryption = $this->choice('Select encryption', ['tls', 'ssl'], 0);
        }

        $fromAddress = $this->ask('Enter from email address', 'noreply@techstore.com');
        $fromName = $this->ask('Enter from name', 'Tech Store');

        // Update .env content
        $envContent = $this->updateEnvValue($envContent, 'MAIL_MAILER', $mailer);
        $envContent = $this->updateEnvValue($envContent, 'MAIL_HOST', $host);
        $envContent = $this->updateEnvValue($envContent, 'MAIL_PORT', $port);
        $envContent = $this->updateEnvValue($envContent, 'MAIL_USERNAME', $username);
        $envContent = $this->updateEnvValue($envContent, 'MAIL_PASSWORD', $password);
        $envContent = $this->updateEnvValue($envContent, 'MAIL_ENCRYPTION', $encryption);
        $envContent = $this->updateEnvValue($envContent, 'MAIL_FROM_ADDRESS', $fromAddress);
        $envContent = $this->updateEnvValue($envContent, 'MAIL_FROM_NAME', $fromName);

        // Write back to .env
        File::put($envPath, $envContent);

        $this->info('Email configuration has been set up successfully!');
        
        if ($mailer === 'mailtrap') {
            $this->info('To test the configuration:');
            $this->info('1. Visit https://mailtrap.io/inboxes');
            $this->info('2. Open your inbox');
            $this->info('3. Run: php artisan tinker');
            $this->info('4. Then run: Mail::to("test@example.com")->send(new \App\Mail\TestEmail());');
            $this->info('5. Check your Mailtrap inbox for the test email');
        } else {
            $this->info('Please test the configuration by running: php artisan tinker');
            $this->info('Then run: Mail::to("your-email@example.com")->send(new \App\Mail\TestEmail());');
        }
    }

    protected function updateEnvValue($envContent, $key, $value)
    {
        // Add quotes if value contains spaces
        if (strpos($value, ' ') !== false) {
            $value = '"' . $value . '"';
        }

        // If the key exists, update it
        if (preg_match("/^{$key}=.*/m", $envContent)) {
            return preg_replace("/^{$key}=.*/m", "{$key}={$value}", $envContent);
        }
        
        // If the key doesn't exist, add it
        return $envContent . "\n{$key}={$value}";
    }
}
