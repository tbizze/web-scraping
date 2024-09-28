<?php

use App\Http\Controllers\ImageController;
use App\Http\Controllers\QrCodeController;
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

Route::get('/ocr', [QrCodeController::class, 'index'])->name('ocr.index');
Route::get('/lsqrcode', [QrCodeController::class, 'scanQrReceipts'])->name('ocr.lsqrcode');
Route::get('/convert/{id}', [QrCodeController::class, 'convert'])->name('ocr.convert');
Route::get('/lsconvert', [QrCodeController::class, 'listaConvert'])->name('ocr.lsconvert');
Route::get('/test-job', [QrCodeController::class, 'testJob'])->name('ocr.testjob');

Route::post('/image/upload', [ImageController::class, 'upload'])->name('image.upload');

Route::get('/notices', [ImageController::class, 'notices'])->name('image.notices');
Route::get('/identificador/{id}', [ImageController::class, 'getIdentificador'])->name('image.identificador');
