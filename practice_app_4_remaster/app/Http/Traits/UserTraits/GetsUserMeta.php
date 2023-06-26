<?php

namespace App\Http\Traits\UserTraits;

trait GetsUserMeta
{
    public function getAllPermissionNames(): array
    {
        $result = $this->permissions->pluck('name')->toArray();
        foreach ($this->roles as $role) {
            $result = array_merge($result, $role->permissions->pluck('name')->toArray());
        }
        return array_unique($result);
    }
}
