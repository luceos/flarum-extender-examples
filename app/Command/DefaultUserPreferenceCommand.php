<?php

namespace App\Command;

use Flarum\User\User;
use Illuminate\Console\Command;

class DefaultUserPreferenceCommand extends Command
{
    protected $signature = 'user-preference {preference} {value}
        {--all : Apply to all users}
        {--user-id= : Apply to one user}';

    public function handle()
    {
        if (! $this->option('all') && ! $this->option('user-id')) {
            $this->error('Apply either --user-id or --all');
            return Command::FAILURE;
        }

        $preference = $this->argument('preference');
        $value = $this->argument('value');

        User::query()
            ->withoutGlobalScopes()
            ->when($this->option('user-id'), fn($query, $id) => $query->where('id', $id))
            ->each(function (User $user) use ($preference, $value) {
                $user->setPreference($preference, $value);
                $user->save();
            });
    }
}
