<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;


class AuthController extends Controller
{
     public function login(): View
     {
        
       return view('auth.login');

     }


   public function authenticate(Request $request):RedirectResponse|string
   {
    
        $credentials = $request->validate(

          [
            'username' => 'required|min:3|max:30',
            'password' =>  'required|min:8|max:32|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'
          ],
          [
            
            'username.required' =>'Es requerido Username',
            'username.min' =>'Minimo de 3 letras username',
            'username.max' =>'Maximo de 30 letras username',

            'password.required' =>'Es requerido password',
            'password.min' =>'Minimo 8 caracter clave',
            'password.max' =>'Maximo 32 caracter clave',
            'password.regex' =>'una letra miniscula, una letra mayuscula, un numero',

          ],


         );

            /* if(Auth::attempt($credential))
              {
                $request->session()->regenerate();
                return redirect()->route('home');
              }
              */

          $user = User::where('username', $credentials['username'])
          ->where('active', true)
          ->where(function($query){
                 $query->whereNull('blocked_until')
                  ->orWhere('blocked_until', '<=', now());
  
           })
           ->whereNotNull('email_verified_at')
           ->whereNull('deleted_at')
           ->first();


           if(!$user)
           {
              return back()->withInput()->with([
                 
                   'invalid_login' => 'Login invalido.'
           
              ]);
           
            }

           if(!password_verify($credentials['password'], $user->password))
            {

               return back()->withInput()->with([
                 
                'invalid_login' => 'Login invalido..'
        
               ]);

            }


            $user->last_login_at = now();
            $user->blocked_until = null;
            $user->save();


            $request->session()->regenerate();
            Auth::login($user);
            return redirect()->intended(route('home'));

   }

   public function logout():RedirectResponse
   {

      Auth::logout();
      return redirect()->route('login');

   }

}
