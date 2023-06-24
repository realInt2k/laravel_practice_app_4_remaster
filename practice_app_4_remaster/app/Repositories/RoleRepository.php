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

    public function saveNewRole($saveData)
    {
        return $this->create($saveData);
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
        return $this->model->withPermissions()
            ->whereId($searchData['id'])
            ->whereName($searchData['name'])
            ->wherePermissionName($searchData['permission'])
            ->paginate($searchData['perPage']);
    }
}
