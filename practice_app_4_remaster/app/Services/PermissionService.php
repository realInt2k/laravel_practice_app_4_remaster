<?php

namespace App\Services;

use App\Repositories\PermissionRepository;

class PermissionService extends BaseService
{
    protected $permisssionRepo;

    public function __construct(
        PermissionRepository $permisssionRepo
    ) {
        $this->permisssionRepo = $permisssionRepo;
    }

    public function getAllPermissions()
    {
        return $this->permisssionRepo->all();
    }
}
