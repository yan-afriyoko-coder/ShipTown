<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\User;

class SetEveryoneAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:set-everyone-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assigns the admin role to all users.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $users = User::all();

        foreach ($users as $user) {
            $user->assignRole('admin');
            $this->info('Assigned user: ' . $user->name . ' as admin.');
        }
    }
}
