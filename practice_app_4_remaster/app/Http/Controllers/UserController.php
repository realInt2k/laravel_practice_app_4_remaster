<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Services\RoleService;
use App\Services\UserService;
use Illuminate\Http\Response;
use App\Services\PermissionService;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    public $userService, $roleService, $permissionService;
    public function __construct(
        UserService $userService,
        RoleService $roleService,
        PermissionService $permissionService
    ) {
        $this->userService = $userService;
        $this->roleService = $roleService;
        $this->permissionService = $permissionService;
    }

    public function index(Request $request)
    {
        return view('pages.users.index');
    }

    public function search(Request $request)
    {
        $users = $this->userService->search($request, self::PER_PAGE);
        $roles = $this->roleService->getAllRoles();
        $permissions = $this->permissionService->getAllPermissions();
        $oldSearch = $request->all();
        $viewHtml = view(
            'pages.users.pagination',
            compact('users', 'roles', 'permissions', 'oldSearch')
        )->render();
        return $this->responseWithHtml($viewHtml);
    }

    public function show(Request $request, $id)
    {
        $user = $this->userService->getById($id);
        $viewHtml = view('pages.users.show', compact('user'))->render();
        return $this->responseWithHtml($viewHtml);
    }

    public function create()
    {
        $roles = $this->roleService->getAllRoles();
        $permissions = $this->permissionService->getAllPermissions();
        $viewHtml = view('pages.users.create', compact('roles', 'permissions'))->render();
        return $this->responseWithHtml($viewHtml);
    }

    public function store(StoreUserRequest $request)
    {
        $user = $this->userService->store($request);
        return $this->responseWithData($user);
    }

    public function edit(Request $request, $id)
    {
        $user = $this->userService->getById($id);
        $roles = $this->roleService->getAllRoles();
        $permissions = $this->permissionService->getAllPermissions();

        $viewHtml = view('pages.users.edit', compact('user', 'roles', 'permissions'))->render();
        return $this->responseWithHtml($viewHtml);
    }

    public function update(UpdateUserRequest $request, $id)
    {
        try {
            $user = $this->userService->update($request, $id);
        } catch (Exception $e) {
            return $this->responseWhenException($request, $e);
        }
        return $this->responseWithData($user);
    }

    public function destroy(Request $request, $id)
    {
        if (auth()->user()->id == $id) {
            auth()->logout();
        }
        try {
            $this->userService->destroy($id);
        } catch (Exception $e) {
            return $this->responseWhenException($request, $e);
        }
        return $this->responseWithHtml('', Response::HTTP_NO_CONTENT);
    }
}
