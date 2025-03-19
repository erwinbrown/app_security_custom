<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    //echo "Hola Mondo";

    DB::connection()->getPdo();
});
