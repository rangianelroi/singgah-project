<?php
namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Siapa yang bisa melihat daftar semua user?
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Siapa yang bisa membuat user baru?
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Siapa yang bisa mengedit user?
     */
    public function update(User $user, User $model): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Siapa yang bisa menghapus user?
     */
    public function delete(User $user, User $model): bool
    {
        return $user->role === 'admin';
    }
}