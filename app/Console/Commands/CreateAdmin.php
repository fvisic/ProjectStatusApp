<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateAdmin extends Command
{
    protected $signature = 'app:create-admin';
    protected $description = 'Create an admin user account';

    public function handle(): int
    {
        $name = $this->ask('Full name');

        $username = $this->ask('Username (lowercase letters, numbers, dots, hyphens)');
        $usernameValidation = Validator::make(['username' => $username], [
            'username' => ['required', 'min:3', 'max:50', 'regex:/^[a-z0-9][a-z0-9._-]*[a-z0-9]$/', 'unique:users,username'],
        ]);
        if ($usernameValidation->fails()) {
            $this->error($usernameValidation->errors()->first('username'));
            return self::FAILURE;
        }

        $email = $this->ask('Email address');
        $emailValidation = Validator::make(['email' => $email], ['email' => 'required|email|unique:users,email']);
        if ($emailValidation->fails()) {
            $this->error($emailValidation->errors()->first('email'));
            return self::FAILURE;
        }

        $password = $this->secret('Password (min 8 characters)');
        if (strlen($password) < 8) {
            $this->error('Password must be at least 8 characters.');
            return self::FAILURE;
        }

        $confirm = $this->secret('Confirm password');
        if ($password !== $confirm) {
            $this->error('Passwords do not match.');
            return self::FAILURE;
        }

        $user = User::create([
            'name'     => $name,
            'username' => $username,
            'email'    => $email,
            'password' => bcrypt($password),
            'role'     => 'admin',
            'is_admin' => true,
            'locale'   => 'en',
        ]);
        $user->email_verified_at = now();
        $user->save();

        $this->info("Admin account created: {$username} <{$email}>");

        return self::SUCCESS;
    }
}
