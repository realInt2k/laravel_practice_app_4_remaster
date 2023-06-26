<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

/**
 * Class UserRepository.
 */
class UserRepository extends BaseRepository
{
    const USERS_PER_PAGE = 15;

    public function model(): string
    {
        return User::class;
    }

    public function saveNewUser(array $storeData): User
    {
        return $this->create($storeData);
    }

    public function updateUser(array $updateData, int $id): User
    {
        /** @var User $user*/
        $user = $this->update($updateData, $id);
        return $user;
    }

    public function destroy(int $id): User
    {
        $user = $this->findOrFail($id);
        $user->delete();
        return $user;
    }

    public function search(array $searchData): LengthAwarePaginator
    {
        return $this->model->withRolesAndPermissions()
            ->whereId($searchData['id'])
            ->whereName($searchData['name'])
            ->whereEmail($searchData['email'])
            ->wherePermissionName($searchData['permission'])
            ->whereRoleName($searchData['role'])
            ->paginate($searchData['perPage'], ['*'], 'page');
    }
}
