<?php

namespace App\Http\Traits\RoleTraits;

trait SetsRoleMeta
{
    public function syncPermissionIds(array $permissionIds): array
    {
        return $this->permissions()->sync($permissionIds);
    }
}
