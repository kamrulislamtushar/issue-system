<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\User;
use App\Model\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class UserController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::latest()->paginate(20);
        return response()->json($users);
    }


    public function create()
    {

    }

    public function store(UserRequest $request)
    {
        $user = User::create($request->except('roles')) ;
        $user->assignRole($request->get('role'));
        return response()->json([
            'message' => "User Created Successfully"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }


    public function edit($id)
    {

    }


    public function update(Request $request, $id)
    {

        $this->validate($request, [
            'name' => 'bail|required|min:2',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'required|integer|unique:users,phone,'.$id,
        ]);
        $user = User::findOrFail($id);
        $user->fill($request->except('roles' ,'permissions' ,'password'));
        if($request->get('password')) {
            $user->password = bcrypt($request->get('password'));
        }

        if ($request->get('role'))
        {
            $user->syncRoles($request->get('role'));
        }

        $user->save();
        return response()->json([
            'message' => "User Updated Successfully"
        ]);
    }

    public function destroy($id)
    {
        if ( Auth::user()->id == $id ) {
            return response()->json([
                'message' => "Logged In User can't be deleted"
            ]);
        }

        if( User::findOrFail($id)->delete() ) {
            return response()->json([
                'message' => "User Deleted Successfully"
            ]);
        }

        return response()->json([
            'message' => "Unable to Delete User!"
        ]);
    }

}
