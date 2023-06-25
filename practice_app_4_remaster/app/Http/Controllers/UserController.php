<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Services\PermissionService;
use App\Services\ProductService;
use App\Services\RoleService;
use App\Services\UserService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application as FoundationApplication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public PermissionService $permissionService;
    public RoleService $roleService;
    public UserService $userService;
    public ProductService $productService;

    public function __construct(
        UserService       $userService,
        RoleService       $roleService,
        PermissionService $permissionService,
        ProductService    $productService
    )
    {
        $this->userService = $userService;
        $this->roleService = $roleService;
        $this->permissionService = $permissionService;
        $this->productService = $productService;
    }

    public function index(): View|FoundationApplication|Factory|Application
    {
        $roles = $this->roleService->getAllRoles();
        return view('pages.users.index', compact('roles'));
    }

    public function search(Request $request): JsonResponse
    {
        $users = $this->userService->search($request, self::PER_PAGE);
        $roles = $this->roleService->getAllRoles();
        $permissions = $this->permissionService->getAllPermissions();
        $viewHtml = view(
            'pages.users.pagination',
            compact('users', 'roles', 'permissions')
        )->render();
        return $this->responseJSON($viewHtml);
    }

    public function show(int $id): JsonResponse
    {
        $user = $this->userService->getById($id);
        $viewHtml = view('pages.users.show', compact('user'))->render();
        return $this->responseJSON($viewHtml);
    }

    public function create(): JsonResponse
    {
        $roles = $this->roleService->getAllRoles();
        $permissions = $this->permissionService->getAllPermissions();
        $viewHtml = view('pages.users.create', compact('roles', 'permissions'))->render();
        return $this->responseJSON($viewHtml);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->store($request);
        return $this->responseJSON($user);
    }

    public function edit(int $id): JsonResponse
    {
        $user = $this->userService->getById($id);
        $roles = $this->roleService->getAllRoles();
        $permissions = $this->permissionService->getAllPermissions();
        $viewHtml = view('pages.users.edit', compact('user', 'roles', 'permissions'))->render();
        return $this->responseJSON($viewHtml);
    }

    /**
     * @throws Exception
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $id = auth()->user()->id;
            $user = $this->userService->update($request, $id, true);
        } catch (Exception $e) {
            DB::rollBack();
            $this->throwException('cannot update user through profile', $e);
        }
        DB::commit();
        return $this->responseJSON($user);
    }

    /**
     * @throws Exception
     */
    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = $this->userService->update($request, $id);
        } catch (Exception $e) {
            DB::rollBack();
            $this->throwException('cannot update user', $e);
        }
        DB::commit();
        return $this->responseJSON($user);
    }

    /**
     * @throws Exception
     */
    public function destroy($id): JsonResponse
    {
        if (auth()->user()->id == $id) {
            auth()->logout();
        }
        DB::beginTransaction();
        try {
            $user = $this->userService->destroy($id);
        } catch (Exception $e) {
            DB::rollBack();
            $this->throwException('cannot destroy user', $e);
        }
        DB::commit();
        $this->productService->unAttachUser($user->id);
        return $this->responseJSON($user);
    }
}
