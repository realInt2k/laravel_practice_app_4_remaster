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
    const PER_PAGE = 15;
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
        $pathWithSearchParam = $this->getSearchString($request);
        if ($pathWithSearchParam == self::DEFAULT_SEARCH_STRING) {
            $pathWithSearchParam = 'users';
        }
        $users = $this->userService->search($request, self::PER_PAGE, $pathWithSearchParam);
        $roles = $this->roleService->getAllRoles();
        $permissions = $this->permissionService->getAllPermissions();
        $oldFilter = $request->all();
        return view(
            'users.index',
            compact('users', 'roles', 'permissions', 'oldFilter')
        );
    }

    public function show(Request $request, $id)
    {
        $user = $this->userService->getById($id);
        $redirectRequest = $request->all();
        return view('users.show', compact('user', 'redirectRequest'));
    }

    public function create()
    {
        $roles = $this->roleService->getAllRoles();
        $permissions = $this->permissionService->getAllPermissions();
        return view('users.create', compact('roles', 'permissions'));
    }

    public function storeAjaxValidation(StoreUserRequest $request)
    {
        if (!$request->ajax()) {
            return redirect()->back();
        }
        return response(Response::HTTP_OK);
    }

    public function store(StoreUserRequest $request)
    {
        try {
            $user = $this->userService->store($request);
        } catch (Exception $e) {
            return $this->responseWhenException($request, $e);
        }
        return redirect()->route('users.show', $user->id);
    }

    public function edit(Request $request, $id)
    {
        $user = $this->userService->getById($id);
        $roles = $this->roleService->getAllRoles();
        $permissions = $this->permissionService->getAllPermissions();
        $redirectRequest = $request->all();
        return view('users.edit', compact('user', 'roles', 'permissions', 'redirectRequest'));
    }

    public function updateAjaxValidation(UpdateUserRequest $request, $id)
    {
        if (!$request->ajax()) {
            return redirect()->back();
        }
        try {
            $user = $this->userService->getById($id);
        } catch (Exception $e) {
            return $this->responseWhenException($request, $e);
        }
        return response(Response::HTTP_OK);
    }

    public function updateAjax(UpdateUserRequest $request, $id)
    {
        if (!$request->ajax()) {
            return redirect()->back();
        }
        try {
            $user = $this->userService->update($request, $id);
        } catch (Exception $e) {
            return $this->responseWhenException($request, $e);
        }
        return response()->json([
            'data' => $user
        ], Response::HTTP_OK);
    }

    public function update(UpdateUserRequest $request, $id)
    {
        try {
            $user = $this->userService->update($request, $id);
        } catch (Exception $e) {
            return $this->responseWhenException($request, $e);
        }
        return redirect()->back();
    }

    public function destroyAjax(Request $request, $id)
    {
        if (auth()->user()->id == $id) {
            auth()->logout();
        }
        try {
            $this->userService->destroy($id);
        } catch (Exception $e) {
            return $this->responseWhenException($request, $e);
        }
        return response(Response::HTTP_NO_CONTENT);
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
        return redirect()->back();
    }
}
