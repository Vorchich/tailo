<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\User;

class PermissionService
{
    public function giveAccess(User $user, $model, bool $canEdit = false): Permission
    {
        // return $model->permissions()->updateOrCreate([
        //     'user_id' => $user->id,
        //     'can_edit' => $canEdit,
        // ]);
    }

    public function removeAccess(User $user, $permission)
    {
        // $user->permission()->findOrFail($permission)->delete();

        // return $model->permissions()->create([
        //     'user_id' => $user->id,
        //     'can_edit' => $canEdit,
        // ]);
    }
}

