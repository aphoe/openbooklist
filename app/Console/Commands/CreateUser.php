<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user if the users table is empty.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (User::query()->exists()) {
            error('Users already exist in the database. This command can only run if the users table is empty.');

            return self::FAILURE;
        }

        $firstName = text(
            label: 'What is the user\'s first name?',
            required: true,
        );

        $lastName = text(
            label: 'What is the user\'s last name?',
            required: true,
        );

        do {
            $email = text(
                label: 'What is the user\'s email address?',
                required: true,
                validate: fn (string $value) => match (true) {
                    ! filter_var($value, FILTER_VALIDATE_EMAIL) => 'Please enter a valid email address.',
                    default => null,
                }
            );

            $emailConfirmation = text(
                label: 'Please confirm the email address:',
                required: true,
            );

            if ($email !== $emailConfirmation) {
                error('Email addresses do not match. Please try again.');
            }
        } while ($email !== $emailConfirmation);

        do {
            $passwordValue = password(
                label: 'What is the user\'s password?',
                required: true,
                validate: fn (string $value) => match (true) {
                    strlen($value) < 8 => 'The password must be at least 8 characters.',
                    default => null,
                }
            );

            $passwordConfirmation = password(
                label: 'Please confirm the password:',
                required: true,
            );

            if ($passwordValue !== $passwordConfirmation) {
                error('Passwords do not match. Please try again.');
            }
        } while ($passwordValue !== $passwordConfirmation);

        $user = User::query()->create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'password' => Hash::make($passwordValue),
            'email_verified_at' => now(),
        ]);

        info("User {$user->first_name} {$user->last_name} created successfully.");

        return self::SUCCESS;
    }
}
