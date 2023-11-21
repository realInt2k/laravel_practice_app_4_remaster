<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserService extends BaseService
{

    protected UserRepository $userRepo;

    public function __construct(
        UserRepository $userRepo,
    )
    {
        $this->userRepo = $userRepo;
    }

    public function getById(int $id): User
    {
        return $this->userRepo->findOrFail($id);
    }

    public function store(Request $request): User
    {
        $storeData = $request->all();
        $storeData = $this->extractRoleOrPermissionInput($storeData);
        $user = $this->userRepo->saveNewUser($storeData);
        $this->syncPermissionsIfSuperAdmin($storeData, $user);
        return $user;
    }

    private function extractRoleOrPermissionInput(array $input): array
    {
        if (!isset($input['roles'])) {
            $input['roles'] = [];
        }
        if (!isset($input['permissions'])) {
            $input['permissions'] = [];
        }
        return $input;
    }

    private function syncPermissionsIfSuperAdmin(array $data, User $targetUser): void
    {
        $targetUser->syncRoles($data['roles']);
        $targetUser->syncPermissions($data['permissions']);
    }

    public function update(Request $request, int $id, bool $isProfile = false): User
    {
        $updateData = $request->all();
        $user = $this->userRepo->findOrFail($id);
        $updateData = $this->getUpdateDataPassword($updateData, $user);
        if ($isProfile) {
            $user = $this->userRepo->updateUser($updateData, $id);
        } else {
            $updateData = $this->extractRoleOrPermissionInput($updateData);
            $user = $this->userRepo->updateUser($updateData, $id);
            $this->syncPermissionsIfSuperAdmin($updateData, $user);
        }
        return $user;
    }

    private function getUpdateDataPassword(array $updateData, User $targetUser): array
    {
        if (empty($updateData['password'])) {
            $updateData['password'] = $targetUser->password;
        } else {
            $updateData['password'] = Hash::make($updateData['password']);
        }
        return $updateData;
    }

    public function destroy(int $id): User
    {
        return $this->userRepo->destroy($id);
    }

    public function search(Request $request, int $perPage): LengthAwarePaginator
    {
        $searchData = [];
        $searchData['id'] = $request['id'];
        $searchData['name'] = $request->name;
        $searchData['email'] = $request->email;
        $searchData['permission'] = $request->permission;
        $searchData['role'] = $request->role;
        $searchData['perPage'] = $perPage;
        return $this->userRepo->search($searchData);
    }
}
