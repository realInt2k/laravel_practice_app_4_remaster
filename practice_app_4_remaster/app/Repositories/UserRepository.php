<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * Class UserRepository.
 */
class UserRepository extends BaseRepository
{
    const USERS_PER_PAGE = 15;
    /**
     * @return string
     *  Return the model
     */
    public function model()
    {
        return User::class;
    }

    public function saveNewUser($storeData)
    {
        $user = $this->create($storeData);
        if (auth()->user()->isSuperAdmin()) {
            $user->syncRoles($storeData['roles']);
            $user->syncPermissions($storeData['permissions']);
        }
        return $user;
    }

    public function updateUser($updateData, $id)
    {
        /** @var User */
        $user = $this->update($updateData, $id);
        if (auth()->user()->isSuperAdmin()) {
            $user->syncRoles($updateData['roles']);
            $user->syncPermissions($updateData['permissions']);
        }
        return $user;
    }

    public function destroy($id)
    {
        $user = $this->findOrFail($id);
        foreach ($user->products as $product) {
            $product->update(['user_id' => null]);
        }
        $user->delete();
        return $user;
    }

    public function search($searchData)
    {
        $users = $this->model->withRolesAndPermissions()
            ->whereId($searchData['id'])
            ->whereName($searchData['name'])
            ->whereEmail($searchData['email'])
            ->wherePermissionName($searchData['permission'])
            ->whereRoleName($searchData['role']);
        $users = $users->paginate($searchData['perPage'], ['*'], 'page');
        return $users;
    }
}
