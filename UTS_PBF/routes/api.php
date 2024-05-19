<?php
use App\Http\Controllers\authController;
use App\Http\Controllers\registerController;
use App\Http\Controllers\categoriesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\productsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
    
});

ROUTE::post('/login',[authController::class,'login']);
ROUTE::post('/register',[registerController::class,'register']);

ROUTE::middleware('user')->group(function(){

});

ROUTE::middleware('admin')->group(function(){
    ROUTE:: post('/categories',[categoriesController::class,'create']);
    ROUTE:: get('/categories',[categoriesController::class,'read']);
    ROUTE:: put('/categories/{id}',[categoriesController::class,'update']);
    ROUTE:: delete('/categories/{id}',[categoriesController::class,'delete']);
    
    
});

ROUTE::middleware('auth.jwt')->group(function(){
    ROUTE:: post('/products',[productsController::class,'create']);
    ROUTE:: get('/products', [productsController::class, 'read']);
    ROUTE:: put('/products/{id}',[productsController::class,'update']);
    ROUTE:: delete('/products/{id}',[productsController::class,'delete']);

});

ROUTE::middleware('web')->group(function(){
ROUTE::get('oauth/register', [authController::class, 'redirectGoogle']);
ROUTE::get('oauth/callback', [authController::class, 'callbackGoogle']);
});

