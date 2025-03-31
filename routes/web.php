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

    Route::get('/forgot_password', [AuthController::class, 'forgotPassword'])->name('forgot');
    Route::post('/forgot_password', [AuthController::class, 'SentResetPassword'])->name('reset_password_link');
    
    Route::get('/reset_password/{token}', [AuthController::class, 'emailVerifcaResetPassword'])->name('valida_mail_resepass');
    Route::post('/reset_password', [AuthController::class, 'changesPassword'])->name('changes_password_verificado');
});


Route::middleware('auth')->group(function(){

     Route::get('/', [MainController::class, 'home'])->name('home');

     Route::get('/profile', [AuthController::class, 'profileUser'])->name('profile');
     Route::post('/profile', [AuthController::class, 'profileChangesPassword'])->name('profile_changes_password');
     
     
     
     Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

});