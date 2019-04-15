<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRequest;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;
class AuthController extends Controller
{


    use AuthenticatesUsers;
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function signup(UserRequest $request)
    {

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password
        ]);
        $user->assignRole('User');
        $user->save();
        return response()->json([
            'message' => 'Successfully created user!'
        ], 201);
    }


    /**
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {

        if(is_numeric($request->get('email')))
        {
            $credentials = ['phone'=>$request->get('email'),'password'=>$request->get('password')];
        }
        else
        {
            $credentials = ['email' => $request->get('email'), 'password'=>$request->get('password')];
        }



        if(!Auth::attempt($credentials))
            return response()->json([
                'message' => 'Invalid Credentials'
            ], 401);

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

}
