<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Services\RoleService;
use Illuminate\Http\Response;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Services\PermissionService;

class RoleController extends Controller
{
    const PER_PAGE = 15;
    public $roleService, $permissionService;
    public function __construct(RoleService $roleService, PermissionService $permissionService)
    {
        $this->roleService = $roleService;
        $this->permissionService = $permissionService;
    }

    public function index(Request $request)
    {
        $pathWithSearchParam = $this->getSearchString($request);
        if ($pathWithSearchParam == self::DEFAULT_SEARCH_STRING) {
            $pathWithSearchParam = 'roles';
        }
        $permissions = $this->permissionService->getAllPermissions();
        $roles = $this->roleService->search($request, self::PER_PAGE, $pathWithSearchParam);
        $oldFilter = $request->all();
        return view('roles.index', compact('permissions', 'roles', 'oldFilter'));
    }

    public function show(Request $request, $id)
    {
        $role = $this->roleService->getById($id);
        $permissions = $this->permissionService->getAllPermissions();
        $redirectRequest = $request->all();
        return view('roles.show', compact('role', 'permissions', 'redirectRequest'));
    }

    public function create(Request $request)
    {
        $permissions = $this->permissionService->getAllPermissions();
        return view('roles.create', compact('permissions'));
    }

    public function storeAjaxValidation(StoreRoleRequest $request)
    {
        if (!$request->ajax()) {
            return redirect()->back();
        }
        return response(Response::HTTP_OK);
    }

    public function store(StoreRoleRequest $request)
    {
        try {
            $role = $this->roleService->store($request);
        } catch (Exception $e) {
            return $this->responseWhenException($request, $e);
        }
        return redirect()->route('roles.show', $role->id);
    }

    public function edit(Request $request, $id)
    {
        $role = $this->roleService->getById($id);
        $redirectRequest = $request->all();
        $permissions = $this->permissionService->getAllPermissions();
        return view('roles.edit', compact('role', 'permissions', 'redirectRequest'));
    }

    public function updateAjaxValidation(UpdateRoleRequest $request, $id)
    {
        try {
            $user = $this->roleService->updateAjaxValidation($id);
        } catch (Exception $e) {
            return $this->responseWhenException($request, $e);
        }
        return response(Response::HTTP_OK);
    }

    public function updateAjax(UpdateRoleRequest $request, $id)
    {
        if (!$request->ajax()) {
            return redirect()->back();
        }
        try {
            $role = $this->roleService->update($request, $id);
        } catch (Exception $e) {
            return $this->responseWhenException($request, $e);
        }
        return response()->json([
            'data' => $role
        ], Response::HTTP_OK);
    }

    public function update(UpdateRoleRequest $request, $id)
    {
        try {
            $role = $this->roleService->update($request, $id);
        } catch (Exception $e) {
            return $this->responseWhenException($request, $e);
        }
        return redirect()->back();
    }

    public function destroyAjax(Request $request, $id)
    {
        try {
            $role = $this->roleService->destroy($id);
        } catch (Exception $e) {
            return $this->responseWhenException($request, $e);
        }
        return response(Response::HTTP_NO_CONTENT);
    }

    public function destroy(Request $request, $id)
    {
        try {
            $role = $this->roleService->destroy($id);
        } catch (Exception $e) {
            return $this->responseWhenException($request, $e);
        }
        return redirect()->back();
    }
}
