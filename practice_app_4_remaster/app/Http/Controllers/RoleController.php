<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Services\PermissionService;
use App\Services\RoleService;
use Exception;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public $roleService, $permissionService;

    public function __construct(RoleService $roleService, PermissionService $permissionService)
    {
        $this->roleService = $roleService;
        $this->permissionService = $permissionService;
    }

    public function index(): View|ViewFactory
    {
        $permissions = $this->permissionService->getAllPermissions();
        return view('pages.roles.index', compact('permissions'));
    }

    public function search(Request $request): JsonResponse
    {
        $permissions = $this->permissionService->getAllPermissions();
        $roles = $this->roleService->search($request, self::PER_PAGE);
        $oldFilter = $request->all();
        $viewHtml = view('pages.roles.pagination', compact('permissions', 'roles', 'oldFilter'))->render();
        return $this->responseJSON($viewHtml);
    }

    public function show(int $id): JsonResponse
    {
        $role = $this->roleService->getById($id);
        $viewHtml = view('pages.roles.show', compact('role'))->render();
        return $this->responseJSON($viewHtml);
    }

    public function create(): JsonResponse
    {
        $permissions = $this->permissionService->getAllPermissions();
        $viewHtml = view('pages.roles.create', compact('permissions'))->render();
        return $this->responseJSON($viewHtml);
    }

    public function store(StoreRoleRequest $request): JsonResponse
    {
        $role = $this->roleService->store($request);
        return $this->responseJSON($role);
    }

    public function edit(int $id): JsonResponse
    {
        $role = $this->roleService->getById($id);
        $permissions = $this->permissionService->getAllPermissions();
        $viewHtml = view('pages.roles.edit', compact('role', 'permissions'))->render();
        return $this->responseJSON($viewHtml);
    }

    public function update(UpdateRoleRequest $request, int $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $role = $this->roleService->update($request, $id);
        } catch (Exception $e) {
            DB::rollBack();
            $this->throwException('cannot update role', $e);
        }
        DB::commit();
        return $this->responseJSON($role);
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $role = $this->roleService->destroy($id);
        } catch (Exception $e) {
            DB::rollBack();
            $this->throwException('cannot destroy role', $e);
        }
        return $this->responseJSON($role);
    }
}
