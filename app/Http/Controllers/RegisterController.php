<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;

class RegisterController extends Controller
{

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(RegisterRequest $request)
    {
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'role' => $request->input('role'),
            ]);
        
            $token = $user->createToken('auth_token')->plainTextToken;
        
            // return response()->json(['token' => $token], 201);
            return response()->json([
                'message' => 'User have been registered',
                'code' => 201,
                'error' => false,
                'token' => $token
            ], 201);
        }
    
}
