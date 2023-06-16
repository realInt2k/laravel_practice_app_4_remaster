<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

class UserService extends BaseService
{

    protected $userRepo;

    public function __construct(
        UserRepository $userRepo,
    ) {
        $this->userRepo = $userRepo;
    }

    public function getAllUsers()
    {
        return $this->userRepo->all();
    }

    public function getById($id)
    {
        return $this->userRepo->findOrFail($id);
    }

    public function store(StoreUserRequest $request)
    {
        DB::beginTransaction();
        try {
            $storeData = $request->all();
            $storeData['roles'] = $this->extractRoleOrPermissionInput($storeData['roles']);
            $storeData['permissions'] = $this->extractRoleOrPermissionInput($storeData['permissions']);
            $user = $this->userRepo->saveNewUser($storeData);
            
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            throw new InvalidArgumentException("cannot create user data");
        }
        DB::commit();
        return $user;
    }

    public function update(UpdateUserRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $updateData = $request->all();
            $user = $this->userRepo->findOrFail($id);
            if ($updateData['password'] === null || empty($updateData['password'])) {
                $updateData['password'] = $user->password;
            } else {
                $updateData['password'] = Hash::make($updateData['password']);
            }
            $updateData['roles'] = $this->extractRoleOrPermissionInput($updateData['roles']);
            $updateData['permissions'] = $this->extractRoleOrPermissionInput($updateData['permissions']);
            $user = $this->userRepo->updateUser($updateData, $id);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            throw new InvalidArgumentException("cannot update user data");
        }
        DB::commit();

        return $user;
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $this->userRepo->destroy($id);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            throw new InvalidArgumentException("cannot destroy user data");
        }
        DB::commit();
    }

    public function search(Request $request, int $perPage, string $path)
    {
        $searchData = [];
        $searchData['id'] = $request->id;
        $searchData['name'] = $request->name;
        $searchData['email'] = $request->email;
        $searchData['permission'] = $request->permission;
        $searchData['role'] = $request->role;
        $searchData['perPage'] = $perPage;
        $searchData['path'] = $path;
        $users = $this->userRepo->search($searchData);
        return $users;
    }

    public function getPaginatedUsers($users, $perPage, $path)
    {
        return $this->userRepo->customPaginate($users, $perPage, null, ['path' => $path]);
    }

    private function extractRoleOrPermissionInput($input)
    {
        if (is_string($input)) {
            return explode(',', $input);
        } elseif ($input == null || $input == '') {
            return [];
        } else {
            return $input;
        }
    }
}
