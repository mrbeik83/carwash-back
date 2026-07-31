<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(): View
    {
        setPermissionsTeamId(null);

        return view('admin.roles.index', [
            'roles' => Role::query()
                ->with('permissions')
                ->orderBy('name')
                ->get(),
            'permissionGroups' => Permission::query()
                ->orderBy('name')
                ->get()
                ->groupBy(fn (Permission $permission) =>
                    str($permission->name)->before('.')->toString()),
        ]);
    }
}
