<?php

namespace App\Repositories;

use App\Models\Role;

/**
 * Class RoleRepository.
 */
class RoleRepository extends BaseRepository
{
    const ROLES_PER_PAGE = 15;
    /**
     * @return string
     *  Return the model
     */
    public function model()
    {
        return Role::class;
    }

    public function indexCore()
    {
        $roles = $this->model->withPermissions()->get();
        return $roles;
    }

    public function saveNewRole(array $saveData)
    {
        $role = $this->create($saveData);
        return $role;
    }

    public function updateRole($updateData, $id)
    {
        $role = $this->findOrFail($id);
        $role->update($updateData);
        return $role;
    }

    public function destroyRole($id)
    {
        $role = $this->findOrFail($id);
        $role->delete();
        return $role;
    }

    public function search($searchData)
    {
        $roles = $this->model->withPermissions()
            ->whereId($searchData['id'])
            ->whereName($searchData['name'])
            ->wherePermissionName($searchData['permission']);
        $roles = $roles->paginate($searchData['perPage']);
        return $roles;
    }
}
