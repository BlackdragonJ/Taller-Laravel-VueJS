<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UsuarioController;
use App\Http\Controllers\Api\AuthController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
   Route::post('/login', [AuthController::class, 'login']);
});  // la ruta principal para el login no solicita el token por eso la dejamos fuera 
 
Route::prefix('v1')
   ->middleware(['auth:sanctum']) 
   ->group(function () {
       Route::get('/me', [AuthController::class, 'me']);
       Route::post('/logout', [AuthController::class, 'logout']);
       // CRUD Usuarios 
       Route::prefix('usuarios')->group(function () {
           Route::get('/listUsers', [UsuarioController::class, 'index']);
           Route::post('/addUser', [UsuarioController::class, 'store']);
           Route::get('/getUser/{id}', [UsuarioController::class, 'show']);
           Route::put('/updateUser/{id}', [UsuarioController::class, 'update']);
           Route::delete('/deleteUser/{id}', [UsuarioController::class, 'destroy']);
       });
   });