<?php

namespace App\Console\Commands;

use App\Models\User;c
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdmin extends Command
{
    protected $signature = 'app:create-admin';

    protected $description = 'Create the portfolio administrator account';

    public function handle(): int
    {
        $name = trim($this->ask('Administrator name'));
        $email = strtolower(trim($this->ask('Administrator email')));

        if ($name === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Please enter a valid name and email address.');

            return self::FAILURE;
        }

        if (User::where('email', $email)->exists()) {
            $this->error('A user with this email already exists.');

            return self::FAILURE;
        }

        $password = $this->secret('Password (minimum 12 characters)');
        $confirmation = $this->secret('Confirm password');

        if (
            ! is_string($password) ||
            strlen($password) < 12 ||
            $password !== $confirmation
        ) {
            $this->error('Passwords must match and contain at least 12 characters.');

            return self::FAILURE;
        }

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ]);

        $this->info('Administrator account created successfully.');

        return self::SUCCESS;
    }
}
