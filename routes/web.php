<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route:: middleware ( 'auth:sanctum' )-> get ( '/dashboard' , function () { 
    // Logique de routage ici 
});