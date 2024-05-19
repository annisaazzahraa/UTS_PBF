<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Validator; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Firebase\JWT\JWT;
use Carbon\Carbon; 
use Laravel\Socialite\Facades\Socialite;


class authController extends Controller
{
    //
    public function login(Request $request){
        
        $validator = Validator::make($request->all(),[
            'email'=>'required|email',
            'password'=>'required'
        ]);
       
        if($validator->fails()){
            return response()->json($validator->messages(),422);
        }

        
        if(Auth::attempt($validator->validated())){
           
            $payload = [
                'name'=> Auth::user()->name,
                'role'=> Auth::user()->role,
                'email'=> Auth::user()->email,
                'iat'=> Carbon::now()->timestamp,
                'exp'=> Carbon::now()->timestamp + 60*60*2 

            ];
            
            $jwt = JWT::encode($payload,env('JWT_SECRET_KEY'),'HS256');
          
            return response()->json([
                'messages'=>'Token Berhasil digenerate',
                'name'=>Auth::user()->name,
                'token'=>'Bearer '.$jwt
            ],200);
        }

        return response()->json(
            ['messages'=>"Pengguna tidak ditemukan"],422
        );
        
    }

    public function redirectGoogle(){
        return Socialite::driver('google')->redirect();
    }

    public function callbackGoogle(){
        try {
            $user = Socialite::driver('google')->user();
            $cekUser = User::where('email', $user->email)->first();
            if ($cekUser) {
                Auth::login($cekUser);
                $payload = [
                    'name'=> Auth::user()->name,
                    'role'=> Auth::user()->role,
                    'email'=> Auth::user()->email,
                    'iat'=> Carbon::now()->timestamp,
                    'exp'=> Carbon::now()->timestamp + 60*60*2 
                ];
                $jwt = JWT::encode($payload,env('JWT_SECRET_KEY'),'HS256');
          
                 return response()->json([
                    'messages'=>'Login berhasil',
                    'name'=>Auth::user()->name,
                    'token'=>'Bearer '.$jwt
                ],200);
            }
            $newUser = User::create([
                'name' => $user->name,
                'email' =>$user->email,
                'password' => bcrypt('zahra1111'. $user->email. $user->name)
            ]);
            $payload = [
                'name'=> Auth::user()->name,
                'role'=> Auth::user()->role,
                'email'=> Auth::user()->email,
                'iat'=> Carbon::now()->timestamp,
                'exp'=> Carbon::now()->timestamp + 60*60*2 
            ];
            $jwt = JWT::encode($payload,env('JWT_SECRET_KEY'),'HS256');
      
             return response()->json([
                'messages'=>'Login dan Register berhasil',
                'name'=>Auth::user()->name,
                'token'=>'Bearer '.$jwt
            ],200);
        } catch (\Exception $e) {
            return redirect()->away('http://127.0.0.1:8000/api/oauth/register');
        }
    }
}
