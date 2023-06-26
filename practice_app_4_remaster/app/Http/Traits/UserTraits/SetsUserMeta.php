<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

Trait SetsUserMeta
{
    public function syncRoles(array $roleIds): array
    {
        return $this->roles()->sync($roleIds);
    }

    public function syncPermissions(Collection|Model|array $permissionIds): array
    {
        return $this->permissions()->sync($permissionIds);
    }
}
