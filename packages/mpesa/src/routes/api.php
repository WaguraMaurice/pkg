<?php

use Illuminate\Support\Facades\Route;
use Montanabay39\Mpesa\Http\Controllers\LNMO_Controller;
use Montanabay39\Mpesa\Http\Controllers\C2B_Controller;
use Montanabay39\Mpesa\Http\Controllers\B2C_Controller;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// all callback route's initialize from safaricom servers and should be secured with the right ssl to work.

Route::group(['middleware' => 'api', 'prefix' => 'api/vendor/mpesa/'], function () {
    // MPESA LNMO ROUTES
    Route::post('LMNO', [LNMO_Controller::class, 'transaction'])->name('mpesa.lnmo');
    Route::post('LMNO/callback', [LNMO_Controller::class, 'callback'])->name('mpesa.lnmo.callback');
    Route::post('LMNO/query', [LNMO_Controller::class, 'query'])->name('mpesa.lnmo.query');
    // MPESA C2B ROUTES
    Route::get('C2B/register', [C2B_Controller::class, 'register'])->name('mpesa.c2b.register'); // use/hit only once.
    Route::post('C2B', [C2B_Controller::class, 'transaction'])->name('mpesa.c2b');
    Route::post('C2B/validation/callback', [C2B_Controller::class, 'validation'])->name('mpesa.c2b.validation.callback');
    Route::post('C2B/confirmation/callback', [C2B_Controller::class, 'confirmation'])->name('mpesa.c2b.confirmation.callback');
    Route::post('C2B/status', [C2B_Controller::class, 'status'])->name('mpesa.c2b.status');
    Route::post('C2B/status/callback', [C2B_Controller::class, 'statusCallback'])->name('mpesa.c2b.status.callback');
    Route::post('C2B/reverse', [C2B_Controller::class, 'reverse'])->name('mpesa.c2b.reverse');
    Route::post('C2B/reverse/callback', [C2B_Controller::class, 'reverseCallback'])->name('mpesa.c2b.reverse.callback');
    Route::get('C2B/balance', [C2B_Controller::class, 'balance'])->name('mpesa.c2b.balance');
    Route::post('C2B/balance/callback', [C2B_Controller::class, 'balanceCallback'])->name('mpesa.c2b.balance.callback');
    // MPESA B2C ROUTES
    Route::post('B2C', [B2C_Controller::class, 'transaction'])->name('mpesa.b2c');
    Route::post('B2C/callback', [B2C_Controller::class, 'callback'])->name('mpesa.b2c.callback');
    Route::post('B2C/status', [B2C_Controller::class, 'status'])->name('mpesa.b2c.status');
    Route::post('B2C/status/callback', [B2C_Controller::class, 'statusCallback'])->name('mpesa.b2c.status.callback');
    Route::post('B2C/reverse', [B2C_Controller::class, 'reverse'])->name('mpesa.b2c.reverse');
    Route::post('B2C/reverse/callback', [B2C_Controller::class, 'reverseCallback'])->name('mpesa.b2c.reverse.callback');
    Route::get('B2C/balance', [B2C_Controller::class, 'balance'])->name('mpesa.b2c.balance');
    Route::post('B2C/balance/callback', [B2C_Controller::class, 'balanceCallback'])->name('mpesa.b2c.balance.callback');
});
