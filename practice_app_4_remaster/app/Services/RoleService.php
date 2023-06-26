<?php

namespace App\Services;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Repositories\RoleRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class RoleService extends BaseService
{
    protected RoleRepository $roleRepo;

    public function __construct(
        RoleRepository $roleRepo,
    ) {
        $this->roleRepo = $roleRepo;
    }

    public function getAllRoles(): Collection
    {
        return $this->roleRepo->all();
    }

    public function getById(int $id): Role
    {
        return $this->roleRepo->findOrFail($id);
    }

    public function store(Request $request): Role
    {
        $saveData = $request->all();
        if (!isset($saveData['permissions'])) {
            $saveData['permissions'] = [];
        }
        $role = $this->roleRepo->saveNewRole($saveData);
        $role->syncPermissionIds($saveData['permissions']);
        return $role;
    }

    public function update(Request $request, int $id): Role
    {
        $updateData = $request->all();
        if (!isset($updateData['permissions'])) {
            $updateData['permissions'] = [];
        }
        $role = $this->roleRepo->updateRole($updateData, $id);
        $role->syncPermissionIds($updateData['permissions']);
        return $role;
    }

    public function destroy(int $id): Role
    {
        return $this->roleRepo->destroyRole($id);
    }

    public function search(Request $request, int $perPage): LengthAwarePaginator
    {
        $searchData = [];
        $searchData['id'] = $request->id;
        $searchData['name'] = $request->name;
        $searchData['permission'] = $request->permission;
        $searchData['perPage'] = $perPage;
        return $this->roleRepo->search($searchData);
    }
}
