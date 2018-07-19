<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Requests\LoginRequest;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        
        $credentials = $request->only('email', 'password');       
        
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['success' => false, 'error' => 'We cant find an account with this credentials. Please make sure you entered the right information and you have verified your email address.'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['success' => false, 'error' => 'Failed to login, please try again.'], 500);
        }
        $currentUser = Auth::user();                
        $last_login = $currentUser->last_login;
        $project_name = "Belum punya project";
        if($currentUser->project){
            $project_name = $currentUser->project->name;
        }
        $currentUser->last_login = date('Y-m-d H:i:s');
        $currentUser->save();
        return response()->json(['success' => true, 
                                 'data'=> [
                                     'token' => $token ,
                                     'last_login' => $last_login,  
                                     'user_name' => $currentUser->name,    
                                     'project_name' => $project_name,                                  
                                  ]
                                ]);
    }

    
    public function logout(Request $request) {
        $this->validate($request, ['token' => 'required']);
        
        try {
            JWTAuth::invalidate($request->input('token'));
            return response()->json(['success' => true, 'message'=> "You have successfully logged out."]);
        } catch (JWTException $e) {
            return response()->json(['success' => false, 'error' => 'Failed to logout, please try again.'], 500);
        }
    }
}
