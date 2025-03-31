<?php

namespace App\Http\Controllers;

use App\Mail\ResetPassword;
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
      $user->password = bcrypt($request->password);
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


    public function profileUser():View
    {
        return view('auth.profile');
                        // route name profile
    }

    public function profileChangesPassword(Request $request):RedirectResponse|View
    {
         
        $request->validate(
                   [
                     'current_password' => 'required|min:8|max:32|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
                     'new_password' => 'required|min:8|max:32|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/|different:current_password',
                     'new_password_confirmation' => 'required|same:new_password',

                   ],
                   [
                     
                      'current_password.required' =>'Es requerido el password actual.',
                      'current_password.min' =>'Minimo 8 caracter clave',
                      'current_password.max' =>'Maximo 32 caracter clave',
                      'current_password.regex' =>'una letra miniscula, una letra mayuscula, un numero',
                      'new_password.required' =>'Es requerido el password nuevo.',
                      'new_password.min' =>'Minimo 8 caracter clave',
                      'new_password.max' =>'Maximo 32 caracter clave',
                      'new_password.regex' =>'una letra miniscula, una letra mayuscula, al menos un numero',
                      'new_password.different' =>'Password debe ser diferente al Actual',
                      'password_confirmation.required' => 'Confirmación de correo es necesario',
                      'password_confirmation.same' => 'Dede ser igual a new password',

                   ],


            );

            if(!password_verify($request->current_password, Auth::user()->password))
            {

               return back()->with([
                'server_error' => 'El password Actual No coincide.'
              
               ]);


            }
         
            

             $user = Auth::user();
             $user->password = bcrypt($request->new_password);
             $user->save();

             Auth::user()->password = $request->new_password;


             return redirect()->route('profile')->with([

                'success' => 'Cambio de password fue exitoso!'

             ]);
                 /* Mb891Top / usuario01 */

    }

    public function forgotPassword():View
    {

           return view('auth.forgot_password');
             
    }

    public function SentResetPassword(Request $request)
    {
      $request->validate(
        [
          'email' => 'required|email',
        ],
        [

           'email.required' => 'Correo es requerido para el reseteo de clave.',
           'email.email' => 'Debe cumplir con formato correo',


        ]
      
      );


        $mensaje_reset = "Verifique su correo, para poder seguir el proceso de recuperacion de Password";

        $user = User::where('email', $request->email)->first();

          if(!$user) 
          {
              return back()->with([

                  'server_message' => $mensaje_reset

              ]);
            
          }
         
          $user->token = Str::random(64);
          $token_link = route('valida_mail_resepass', ['token' => $user->token]);


          $result = Mail::to($user->email)->send(new ResetPassword($user->username, $token_link));

          if(!$result) 
          {
              return back()->with([

                  'server_message' => $mensaje_reset

              ]);
            
          }

          $user->save();


          return back()->with([

            'server_message' => $mensaje_reset

        ]);
    }

   public function emailVerifcaResetPassword(string $token):RedirectResponse | View
    {
          $usr = User::where('token', $token)->first();

          if(!$usr) 
          {

             return redirect()->route('login');

          }
          
        return view('auth.reset_password_client', ['token' => $token]);
    } 
    
   

    public function changesPassword(Request $request):RedirectResponse | View
    {
    
          $request->validate(
            [
              'tokene' => 'required',
              'new_password' => 'required|min:8|max:32|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/|different:current_password',
              'new_password_confirmation' => 'required|same:new_password',
            ],
            [
              
              'tokene.required' =>'Token es requerido.',
              'new_password.required' =>'Es requerido el password nuevo.',
              'new_password.min' =>'Minimo 8 caracter clave',
              'new_password.max' =>'Maximo 32 caracter clave',
              'new_password.regex' =>'una letra miniscula, una letra mayuscula, al menos un numero',
              'password_confirmation.required' => 'Confirmación de correo es necesario',
              'password_confirmation.same' => 'Dede ser igual a new password',

            ]
        );  

         $user = User::where('token', $request->tokene)->first();

         if(!$user) 
          {

             return redirect()->route('login');

          }

          $user->password = bcrypt($request->new_password);
          $user->token = null;
          $user->save();

        return redirect()->route('login')->with([
       
            'success' => true

        ]);
   } 

}
