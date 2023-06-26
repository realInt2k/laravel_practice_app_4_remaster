<?php

namespace App\Http\Traits\RoleTraits;

trait ChecksRoleMeta
{
    public function existsPermissionId(int $id): bool
    {
        return $this->permissions->find($id) !== null;
    }
}
