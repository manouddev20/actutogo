<?php
use App\Http\Controllers\Api\Web\Frontoffice\HomeController; 
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/



//La route pour la page d'accueil

Route::get('/', [HomeController::class, 'home'] );

Route::view('/ads.txt', 'adsense.ads');