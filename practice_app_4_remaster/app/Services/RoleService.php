<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Repositories\RoleRepository;

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
        $saveData = $request->all();
        if (!isset($saveData['permissions'])) {
            $saveData['permissions'] = [];
        }
        $role = $this->roleRepo->saveNewRole($saveData);
        $role->syncPermissionIds($saveData['permissions']);
        return $role;
    }

    public function update($request, $id)
    {
        DB::beginTransaction();
        try {
            $updateData = $request->all();
            if (!isset($updateData['permissions'])) {
                $updateData['permissions'] = [];
            }
            $role = $this->roleRepo->updateRole($updateData, $id);
            $role->syncPermissionIds($updateData['permissions']);
        } catch (Exception $e) {
            DB::rollBack();
            $this->throwException('cannot update role', $e);
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
            $this->throwException('cannot destroy role', $e);
        }
        DB::commit();
        return $role;
    }

    public function search(Request $request, $perPage)
    {
        $searchData = [];
        $searchData['id'] = $request->id;
        $searchData['name'] = $request->name;
        $searchData['permission'] = $request->permission;
        $searchData['perPage'] = $perPage;
        return $this->roleRepo->search($searchData);
    }
}
