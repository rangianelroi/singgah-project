<?php

namespace App\Policies;

use App\Models\ConfiscatedItem;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ConfiscatedItemPolicy
{
    /**
     * Siapa yang bisa melihat daftar semua barang?
     */
    public function viewAny(User $user): bool
    {
        return true; // Semua peran yang login bisa melihat daftar
    }

    /**
     * Siapa yang bisa membuat barang baru?
     */
    public function create(User $user): bool
    {
        // Hanya operator dan admin
        return in_array($user->role, ['operator_avsec', 'admin']);
    }

    /**
     * Siapa yang bisa mengedit barang?
     */
    public function update(User $user, ConfiscatedItem $confiscatedItem): bool
    {
        // Admin dan Team Leader Investigasi boleh mengedit kapan saja (untuk menambah lokasi, dll)
        if (in_array($user->role, ['admin', 'team_leader_avsec', 'department_head_avsec'])) {
            return true;
        }

        // Squad Leader hanya bisa mengedit jika statusnya masih awal
        if ($user->role === 'squad_leader_avsec') {
            $latestStatus = $confiscatedItem->statusLogs()->latest()->first()?->status;
            return $latestStatus === 'RECORDED';
        }
        return false;
    }

    /**
     * Siapa yang bisa menghapus barang?
     */
    public function delete(User $user, ConfiscatedItem $confiscatedItem): bool
    {
        // Hanya admin yang bisa menghapus
        return $user->role === 'admin';
    }
}
