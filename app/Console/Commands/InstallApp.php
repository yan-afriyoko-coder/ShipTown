<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

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
    protected $description = 'Install Products Management application';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->keyGenerate();

        $this->migrate();

        $this->passportKey();
    }

    private function keyGenerate(): void
    {
        $this->info('php artisan key:generate --ansi --no-interaction');
        $this->call('key:generate', [
            '--ansi',
            '--no-interaction' => true
        ]);
    }

    private function migrate(): void
    {
        $this->info('php artisan migrate --force');
        try {
            $this->call('migrate', [
                '--force' => true
            ]);
        } catch (\Exception $e) {
            $this->error('Could not migrate, please make sure DB connection is configured');
            $this->error($e->getMessage());
        }
    }

    private function passportKey(): void
    {
        $this->info('php artisan passport:keys');
        $this->call('passport:keys', []);
    }
}
