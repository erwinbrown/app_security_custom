<?php

namespace App\Http\Controllers;

use App\Mail\NewUserConfirmation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Illuminate\Support\Str;


class AuthController extends Controller
{
     public function login(): View
     {
        
       return view('auth.login');

     }


    public function authenticate(Request $request):RedirectResponse
    {
      
          $credentials = $request->validate(

            [
              'username' => 'required|min:3|max:30',
              'password' =>  'required|min:8|max:32|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
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

    public function registerUser():View
    {
       return view('auth.register');

    }

    public function storeUser(Request $request):RedirectResponse|View
    {
      $request->validate(
        [
           'username' => 'required|min:3|max:30|unique:users,username',
           'email' => 'required|email|unique:users,email',
           'password' => 'required|min:8|max:32|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
           'password_confirmation' => 'required|same:password',

        ],
        [
              
          'username.required' =>'Es requerido Username',
          'username.min' =>'Minimo de 3 letras username',
          'username.max' =>'Maximo de 30 letras username',
          'username.unique' =>'Username unico',
          'email.required' => 'Correo es obligatorio',
          'email.unique' => 'Correo es unico',
          'email.email'   => 'Debe ser formato valido de un correo',
          'password.required' =>'Es requerido password',
          'password.min' =>'Minimo 8 caracter clave',
          'password.max' =>'Maximo 32 caracter clave',
          'password.regex' =>'una letra miniscula, una letra mayuscula, un numero',
          'password_confirmation.required' => 'Confirmación de correo es necesario',
          'password_confirmation.same' => 'Los campos de la constraseña deben ser iguales',

        ],

      );
    
      $user = new User();
      $user->username = $request->username;
      $user->email = $request->email;
      $user->password = bcrypt('$request->password');
      $user->token = Str::random(64);
      
      $confirmation_link = route('mail_confirmation_user', ['token' => $user->token]);

      $result = Mail::to($user->email)->send(new NewUserConfirmation($user->username, $confirmation_link));


      if(!$result)
      {
        return back()->withInput()->with([
                  
          'server_error' => 'Error al enviar correo.'
  
        ]);

      }
     
      $user->save();

       //dd($user);
      return view('auth.email_sent', ['email' => $user->email ]);

    }

    public function userNewMailConfirmation($token)
    {

          $user = User::where('token', $token)->first();

          if(!$user)
          {
            return redirect()->route('login');

          }

          
          $user->email_verified_at = Carbon::now();
          $user->token = null;
          $user->active = true;
          $user->save();

          Auth::login($user);

          return view('auth.new_user_confirmation');
           
    }

}
