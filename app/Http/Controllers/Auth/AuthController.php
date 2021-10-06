<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Mail\VerifyMail;
use DB;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|string|email|unique:'.config('crudbooster.USER_TABLE'),
            'password' => 'required|string|confirmed',
        ]);
        DB::beginTransaction();
        try{
            DB::table(config('crudbooster.USER_TABLE'))->insert([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'id_cms_privileges' => 5,
            ]);
        }catch(\Exception $e){
            DB::rollback();
            return response()->json(['status' => 'Register User Failed', 'message' => $e->getMessage()]);
        }
        DB::commit();
        
        return response()->json([
            'status' => 'Created',
            'message' => 'Successfully registered!'
        ], 201);
    }

    public function verifyUser($token)
    {
      $verifyUser = User::where('verified_token', $token)->first();
      if(isset($verifyUser) ){
        $user = $verifyUser;
        if(!$user->is_active) {
          $verifyUser->is_active = 1;
          $verifyUser->save();
          $status = "Your e-mail is verified. You can now login.";
        } else {
          $status = "Your e-mail is already verified. You can now login.";
        }
      } else {
        return response()->json([
            'status' => 'Error',
            'message' => 'Sorry your email cannot be identified.'
        ], 200);
      }
      return response()->json([
            'status' => $status,
            'message' => $status
        ], 201);
    }

    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        $credentials = request(['email', 'password']);
        if(Auth::attempt($credentials)){
           if(Auth::user()->is_active == 1){
                $user = $request->user();
                $tokenResult = $user->createToken('Personal Access Token');
                $token = $tokenResult->token;
                $token->save();
                
                if($user->role == 'User'){
                    $user = User::where('id', '=', $user->id)->get();
                    $users =$user->toArray();
                    return response()->json([
                        'status' => 'Success',
                        'token' => $tokenResult->accessToken,
                        'nama' => array_values($users)[0]['name'],
                        // 'foto_profil'=>array_values($members)[0]['image_URL'],
                    ]);
                
                }else {
                    $user = User::where('id', '=', $user->id)->get();
                    $users = $user->toArray();
                    return response()->json([
                        'status' => 'Success',
                        'token' => $tokenResult->accessToken,
                        'role_name' => 'Admin',
                        'nama' => array_values($users)[0]['name'],
                    ]);
                }
           }else if(Auth::user()->is_active == 0){
                return response()->json([
                    'status' => 'Deactive',
                    'message' => 'Akun anda masih belum aktif, silahkan cek email kembali untuk melihat link aktivasi'
                ], 401);
           }
        }else{
            return response()->json([
                'message' => 'Email anda belum terdaftar, silahkan register terlebih dahulu'
            ], 401);
        }
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
    
    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
