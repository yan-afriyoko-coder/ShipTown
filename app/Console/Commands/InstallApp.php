<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 *
 */
class InstallApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the application and generates encryption keys';

    public function handle(): int
    {
        if (env('PASSPORT_PRIVATE_KEY') === '') {
            $this->call('passport:keys', [
                '--force' => true,
            ]);

            $this->setVariable('PASSPORT_PRIVATE_KEY', implode('', ['"', Storage::get('oauth-private.key'), '"']));
            $this->setVariable('PASSPORT_PUBLIC_KEY', implode('', ['"', Storage::get('oauth-public.key'), '"']));
            unlink(storage_path('oauth-private.key'));
            unlink(storage_path('oauth-public.key'));

            $this->info("Passport encryption keys generated successfully");
        }

        return 0;
    }

    /**
     * @param string $key
     * @param string $value
     */
    private function setVariable(string $key, string $value): void
    {
        $path = base_path('.env');

        if (file_exists($path)) {
            file_put_contents($path, str_replace(
                $key . '=' . env($key),
                $key . '=' . $value,
                file_get_contents($path)
            ));
        }
    }
}
