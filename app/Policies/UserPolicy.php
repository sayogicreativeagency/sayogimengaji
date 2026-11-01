<?php

namespace App\Policies;

use App\Models\User;

/** Hanya superadmin yang melihat & mengubah peran akun. Akun dibuat lewat /daftar. */
class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->superadmin();
    }

    public function view(User $user): bool
    {
        return $user->superadmin();
    }

    public function update(User $user): bool
    {
        return $user->superadmin();
    }

    public function create(): bool
    {
        return false;
    }

    public function delete(): bool
    {
        return false;
    }
}
