<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;//Panggil Library JWT
use Firebase\JWT\Key;//Panggil Library JWT Key


class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $jwt = $request->bearerToken();
        
        if($jwt == 'null' || $jwt == ''){
            return response()->json(
                [
                    'messages'=>'Token kosong'
                ],401);
        }else{
            $decoded = JWT::decode($jwt, new KEY(env('JWT_SECRET_KEY'),'HS256'));
        }
        return $next($request);
    }
}
