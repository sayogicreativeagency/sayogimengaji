<?php

namespace App\Policies;

use App\Models\User;

/** Superadmin & editor mengoreksi hadist; hadist hanya masuk/keluar lewat impor. */
class HadistPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->peran->bisaMasukAdmin();
    }

    public function view(User $user): bool
    {
        return $user->peran->bisaMasukAdmin();
    }

    public function update(User $user): bool
    {
        return $user->peran->bisaMasukAdmin();
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
