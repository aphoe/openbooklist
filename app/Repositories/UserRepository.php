<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    /**
     * Update the user's profile information.
     */
    public function updateProfile(User $user, string $firstName, string $lastName, string $email): bool
    {
        $user->first_name = $firstName;
        $user->last_name = $lastName;
        $user->email = $email;

        return $user->save();
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(User $user, string $password): bool
    {
        $user->password = Hash::make($password);

        return $user->save();
    }

    /**
     * Update the user's AI description preference.
     */
    public function updateAiDescription(User $user, bool $useAiDescription): bool
    {
        $user->use_ai_description = $useAiDescription;

        return $user->save();
    }
}
