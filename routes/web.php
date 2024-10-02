<?php

use App\Http\Controllers\ImageController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\TransactionController;
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

    // Rotas de QR Codes.
    Route::get('/comprovantes', [QrCodeController::class, 'index'])->name('comprovantes.index');
    Route::get('/comprovantes/scan-pastas', [QrCodeController::class, 'scanPastas'])->name('comprovantes.scan-pastas');
    Route::get('/comprovantes/convert/{qrCode}', [QrCodeController::class, 'convert'])->name('comprovantes.convert');
    //Route::get('/lsconvert', [QrCodeController::class, 'listaConvert'])->name('ocr.lsconvert');
    Route::get('/comprovantes/export', [QrCodeController::class, 'export'])->name('comprovantes.export');

    // Rotas de transações.
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/import', [TransactionController::class, 'import'])->name('transactions.import');
    Route::post('/transactions/import', [TransactionController::class, 'processImport'])->name('transactions.process.import');
    Route::get('/transactions/import-all', [TransactionController::class, 'importAll'])->name('transactions.import-all');
});

Route::post('/image/upload', [ImageController::class, 'upload'])->name('image.upload');

Route::get('/notices', [ImageController::class, 'notices'])->name('image.notices');
Route::get('/identificador/{id}', [ImageController::class, 'getIdentificador'])->name('image.identificador');
