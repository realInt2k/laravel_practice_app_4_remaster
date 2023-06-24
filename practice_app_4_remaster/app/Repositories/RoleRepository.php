<?php

namespace App\Repositories;

use App\Models\Role;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class RoleRepository.
 */
class RoleRepository extends BaseRepository
{
    const ROLES_PER_PAGE = 15;

    public function model(): string
    {
        return Role::class;
    }

    public function saveNewRole(array $saveData): Role
    {
        return $this->create($saveData);
    }

    public function updateRole(array $updateData, int $id): Role
    {
        $role = $this->findOrFail($id);
        $role->update($updateData);
        return $role;
    }

    public function destroyRole(int $id): Role
    {
        $role = $this->findOrFail($id);
        $role->delete();
        return $role;
    }

    public function search(array $searchData): LengthAwarePaginator
    {
        return $this->model->withPermissions()
            ->whereId($searchData['id'])
            ->whereName($searchData['name'])
            ->wherePermissionName($searchData['permission'])
            ->paginate($searchData['perPage']);
    }
}
