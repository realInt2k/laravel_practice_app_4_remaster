<?php

namespace App\Repositories;

use App\Models\Permission;


class PermissionRepository extends BaseRepository
{
    /**
     * @return string
     *  Return the model
     */
    public function model()
    {
        return Permission::class;
    }
}
