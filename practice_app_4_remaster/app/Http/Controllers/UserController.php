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
use App\Http\Requests\UpdateProfileRequest;

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
        $roles = $this->roleService->getAllRoles();
        return view('pages.users.index', compact('roles'));
    }

    public function search(Request $request)
    {
        $users = $this->userService->search($request, self::PER_PAGE);
        $roles = $this->roleService->getAllRoles();
        $permissions = $this->permissionService->getAllPermissions();
        $viewHtml = view(
            'pages.users.pagination',
            compact('users', 'roles', 'permissions')
        )->render();
        return $this->responseWithData($viewHtml);
    }

    public function show(Request $request, $id)
    {
        $user = $this->userService->getById($id);
        $viewHtml = view('pages.users.show', compact('user'))->render();
        return $this->responseWithData($viewHtml);
    }

    public function create()
    {
        $roles = $this->roleService->getAllRoles();
        $permissions = $this->permissionService->getAllPermissions();
        $viewHtml = view('pages.users.create', compact('roles', 'permissions'))->render();
        return $this->responseWithData($viewHtml);
    }

    public function store(StoreUserRequest $request)
    {
        try {
            $user = $this->userService->store($request);
        } catch (Exception $e) {
            return $this->responseWhenException($request, $e);
        }
        return $this->responseWithData($user);
    }

    public function edit(Request $request, $id)
    {
        $user = $this->userService->getById($id);
        $roles = $this->roleService->getAllRoles();
        $permissions = $this->permissionService->getAllPermissions();

        $viewHtml = view('pages.users.edit', compact('user', 'roles', 'permissions'))->render();
        return $this->responseWithData($viewHtml);
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $id = auth()->user()->id;
        try {
            $user = $this->userService->update($request, $id, true);
        } catch (Exception $e) {
            return $this->responseWhenException($request, $e);
        }
        return $this->responseWithData($user);
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
            $user = $this->userService->destroy($id);
        } catch (Exception $e) {
            return $this->responseWhenException($request, $e);
        }
        return $this->responseWithData($user, Response::HTTP_NO_CONTENT);
    }
}
