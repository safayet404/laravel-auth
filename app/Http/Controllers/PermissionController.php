<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;


class PermissionController extends Controller
{
    public function getAllPermissions()
    {
        $permissions = Permission::pluck('name');

        return response()->json($permissions);
    }
}
