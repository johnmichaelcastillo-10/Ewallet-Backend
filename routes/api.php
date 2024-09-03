<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\Ewallet;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::post("register", [Ewallet::class, "register"]);

Route::post("login", [Ewallet::class, "login"]);

Route::group(["middleware" => ["auth:sanctum"]], function () {
  //profile
  Route::get("profile", [Ewallet::class, "profile"]);

  //logout
  Route::get("logout", [Ewallet::class, "logout"]);
});
