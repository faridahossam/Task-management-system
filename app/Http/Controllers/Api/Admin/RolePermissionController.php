<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateRole;
use App\Http\Requests\UpdateRole;
use App\Http\Resources\RoleResource;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return RoleResource::collection(Role::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateRole $request)
    {
        $requestData = $request->validated();
        $role = RamedaRole::create(['name' => $requestData['name'], 'guard-name' => 'api']);
        if ($requestData['permissions_ids']) {
            $permission_ids = $requestData['permissions_ids'];
            foreach ($permission_ids as $id) {
                $permission = Permission::findorFail($id);
                $role->fresh()->givePermissionTo($permission);
            }
        }
        if ($requestData['users_ids']) {
            $role->users()->syncWithoutDetaching($requestData['users_ids']);
        }

        return new RoleResource($role->fresh());
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRole $request, Role $role)
    {
        $requestData = $request->validated();

        try {
            DB::beginTransaction();

            $permissions = [];
            if (isset($requestData['permissions_ids'])) {
                $permissions = Permission::whereIn('id', $requestData['permission_ids'])->pluck('name')->toArray();
                $role->syncPermissions($permissions);
            }

            $role->update(collect($requestData)->except(['permissions_ids', 'users_ids'])->toArray());

            if (isset($requestData['users_ids'])) {
                $role->users()->sync($requestData['users_ids']);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }

        return new RoleResource($role);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
