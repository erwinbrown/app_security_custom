<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function(){
   
    Route::get('/login', [AuthController::class, 'login'])->name('login');
  Route::post('/login', [AuthController::class, 'authenticate'])->name('authenticate');


    // registro
    
    Route::get('/register', [AuthController::class, 'registerUser'])->name('register_user');
    Route::post('/register', [AuthController::class, 'storeUser'])->name('store_user');
   // confirmar registro del usuario nuevo
    Route::get('/mail_confirmation_user/{token}', [AuthController::class, 'userNewMailConfirmation'])->name('mail_confirmation_user');
});


Route::middleware('auth')->group(function(){

     Route::get('/', [MainController::class, 'home'])->name('home');

     Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

});