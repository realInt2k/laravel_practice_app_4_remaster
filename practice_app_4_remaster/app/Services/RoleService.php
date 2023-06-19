<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Repositories\RoleRepository;
use App\Http\Requests\UpdateRoleRequest;

class RoleService extends BaseService
{
    protected $roleRepo;

    public function __construct(
        RoleRepository $roleRepo,
    ) {
        $this->roleRepo = $roleRepo;
    }

    public function getAllRoles()
    {
        return $this->roleRepo->all();
    }

    public function getById($id)
    {
        $role = $this->roleRepo->findOrFail($id);
        return $role;
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {
            $saveData = $request->all();
            if (!isset($saveData['permissions'])) {
                $saveData['permissions'] = [];
            }
            $role = $this->roleRepo->saveNewRole($saveData);
            /** @var User */
            $authUser = auth()->user();
            if ($authUser->isSuperAdmin()) {
                $role->syncPermissionIds($saveData['permissions']);
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            throw new InvalidArgumentException("cannot update role data");
        }
        DB::commit();
        return $role;
    }

    public function update(UpdateRoleRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $updateData = $request->all();
            if (!isset($updateData['permissions'])) {
                $updateData['permissions'] = [];
            }
            $role = $this->roleRepo->updateRole($updateData, $id);
            /** @var User */
            $authUser = auth()->user();
            if ($authUser->isSuperAdmin()) {
                $role->syncPermissionIds($updateData['permissions']);
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            throw new InvalidArgumentException("cannot update role data");
        }
        DB::commit();
        return $role;
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $role = $this->roleRepo->destroyRole($id);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            throw new InvalidArgumentException("cannot destroy role data");
        }
        DB::commit();
        return $role;
    }

    public function search(Request $request, $perPage, $path)
    {
        $searchData = [];
        $searchData['id'] = $request->id;
        $searchData['name'] = $request->name;
        $searchData['permission'] = $request->permission;
        $searchData['perPage'] = $perPage;
        $searchData['path'] = $path;
        return $this->roleRepo->search($searchData);
    }
}
