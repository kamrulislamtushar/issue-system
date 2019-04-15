<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{

    public function index()
    {
        $roles = Role::all();
        return view('role.index')->with('roles', $roles);
    }

    public function create()
    {

        return view('role.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
                'name'=>'required|unique:roles|max:15',
            ]
        );
        $name = $request['name'];
        $role = new Role();
        $role->name = $name;
        $role->save();

        return response()->json([
            'message' => 'Successfully created user!'
        ], 201);
    }




    public function edit($id)
    {
        $role = Role::findOrFail($id);
        return view('role.edit', compact('role'));
    }


    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $this->validate($request, [
            'name'=>'required|max:10|unique:roles,name,'.$id,
        ]);

        $role->name = $request->get('name');
        $role->save();

        return response()->json([
            'message' => 'Role Updated Successfully!'
        ], 201);
    }


    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();
        return response()->json([
            'message' => 'Role Deleted !'
        ], 201);
    }
}
