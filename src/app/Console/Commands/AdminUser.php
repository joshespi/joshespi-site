<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\password;

/**
 * The only way an admin account comes into being: the site has no sign-up.
 * Running it again for an existing email resets that account's password.
 */
class AdminUser extends Command
{
    protected $signature = 'admin:user
        {email : Login email}
        {--name=Admin : Display name, for a new account}';

    protected $description = 'Create an admin login, or reset its password';

    public function handle(): int
    {
        $email = $this->argument('email');

        if (Validator::make(['email' => $email], ['email' => 'required|email'])->fails()) {
            $this->error("[{$email}] is not a valid email address.");

            return self::INVALID;
        }

        // Prompted, never an option: a password on the command line ends up in
        // shell history.
        $password = password(
            label: 'Password',
            required: true,
            validate: fn (string $value) => strlen($value) < 12 ? 'Use at least 12 characters.' : null,
        );

        $user = User::firstOrNew(['email' => $email]);
        $isNew = ! $user->exists;

        $user->forceFill([
            'name'     => $user->name ?? $this->option('name'),
            'password' => $password,
        ])->save();

        $this->info($isNew ? "Created admin {$email}." : "Reset the password for {$email}.");
        $this->line('Sign in at ' . route('admin.login'));

        return self::SUCCESS;
    }
}
