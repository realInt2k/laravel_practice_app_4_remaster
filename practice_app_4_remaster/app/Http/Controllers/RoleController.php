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
    public $roleService, $permissionService;
    public function __construct(RoleService $roleService, PermissionService $permissionService)
    {
        $this->roleService = $roleService;
        $this->permissionService = $permissionService;
    }

    public function index()
    {
        $permissions = $this->permissionService->getAllPermissions();
        return view('pages.roles.index', compact('permissions'));
    }

    public function search(Request $request)
    {
        $permissions = $this->permissionService->getAllPermissions();
        $roles = $this->roleService->search($request, self::PER_PAGE);
        $oldFilter = $request->all();
        $viewHtml = view('pages.roles.pagination', compact('permissions', 'roles', 'oldFilter'))->render();
        return $this->responseWithData($viewHtml);
    }

    public function show(Request $request, $id)
    {
        $role = $this->roleService->getById($id);
        $viewHtml = view('pages.roles.show', compact('role'))->render();
        return $this->responseWithData($viewHtml);
    }

    public function create(Request $request)
    {
        $permissions = $this->permissionService->getAllPermissions();
        $viewHtml = view('pages.roles.create', compact('permissions'))->render();
        return $this->responseWithData($viewHtml);
    }

    public function store(StoreRoleRequest $request)
    {
        $role = $this->roleService->store($request);
        return $this->responseWithData($role);
    }

    public function edit(Request $request, $id)
    {
        $role = $this->roleService->getById($id);
        $permissions = $this->permissionService->getAllPermissions();
        $viewHtml = view('pages.roles.edit', compact('role', 'permissions'))->render();
        return $this->responseWithData($viewHtml);
    }

    public function update(UpdateRoleRequest $request, $id)
    {
        try {
            $role = $this->roleService->update($request, $id);
        } catch (Exception $e) {
            return $this->responseWhenException($request, $e);
        }
        return $this->responseWithData($role);
    }

    public function destroy(Request $request, $id)
    {
        try {
            $role = $this->roleService->destroy($id);
        } catch (Exception $e) {
            return $this->responseWhenException($request, $e);
        }
        return $this->responseWithData($role, Response::HTTP_NO_CONTENT);
    }
}
