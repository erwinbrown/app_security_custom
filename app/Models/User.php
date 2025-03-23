<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticable;

class User extends Authenticable
{
    // atributos no serializables
    protected $hidden = [

        'password',
        'token'

    ];
        
    
}
