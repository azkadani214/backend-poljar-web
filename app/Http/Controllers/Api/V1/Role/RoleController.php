<?php

namespace App\Http\Controllers\Api\V1\Role;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * Get all roles
     * 
     * @group Role Management
     * @authenticated
     */
    public function index(): JsonResponse
    {
        $roles = Role::with('permissions')->get();
        return ResponseHelper::success($roles, 'Roles retrieved successfully');
    }

    /**
     * Get all available permissions grouped by module
     * 
     * @group Role Management
     * @authenticated
     */
    public function permissions(): JsonResponse
    {
        $permissions = Permission::all()->groupBy('module');
        return ResponseHelper::success($permissions, 'Permissions retrieved successfully');
    }

    /**
     * Update permissions for a role
     * 
     * @group Role Management
     * @authenticated
     */
    public function updatePermissions(Request $request, Role $role): JsonResponse
    {
        $request->validate([
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        try {
            DB::beginTransaction();
            $role->permissions()->sync($request->permission_ids);
            DB::commit();

            return ResponseHelper::success($role->load('permissions'), 'Role permissions updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Store new role
     * 
     * @group Role Management
     * @authenticated
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'label' => 'required|string',
        ]);

        $role = Role::create($request->only('name', 'label'));
        return ResponseHelper::created($role, 'Role created successfully');
    }

    /**
     * Update role
     * 
     * @group Role Management
     * @authenticated
     */
    public function update(Request $request, Role $role): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
            'label' => 'required|string',
        ]);

        $role->update($request->only('name', 'label'));
        return ResponseHelper::updated($role, 'Role updated successfully');
    }

    /**
     * Delete role
     * 
     * @group Role Management
     * @authenticated
     */
    public function destroy(Role $role): JsonResponse
    {
        $role->delete();
        return ResponseHelper::deleted('Role deleted successfully');
    }
}
