<?php

namespace App\Policies;

use App\Models\User;

/** Editor membaca laporan; hanya superadmin yang menghapus. */
class LaporanPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->peran->bisaMasukAdmin();
    }

    public function view(User $user): bool
    {
        return $user->peran->bisaMasukAdmin();
    }

    public function delete(User $user): bool
    {
        return $user->superadmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->superadmin();
    }
}
