<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    // atributos no serializables
    protected $hidden = [

        'password',
        'token'

    ];
        
    
}
