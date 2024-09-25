<?php

use App\Http\Controllers\ImageController;
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

Route::get('/', function () {
    // Para fins de teste, se ambiente local => faz login automático como usuário com ID 1.
    if (app()->isLocal()) {
        auth()->loginUsingId(1);
        return to_route('dashboard');
    }
    // Se for ambiente production, segue normal e não faz login automático.
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::get('/image', [ImageController::class, 'index'])->name('image.index');
Route::post('/image/upload', [ImageController::class, 'upload'])->name('image.upload');

Route::get('/notices', [ImageController::class, 'notices'])->name('image.notices');
