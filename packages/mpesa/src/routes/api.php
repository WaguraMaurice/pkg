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

Route::group(['middleware' => 'api', 'prefix' => 'api/vendor/daraja'], function () {
    // MPESA LNMO ROUTES
    Route::post('LMNO/transaction', [LNMO_Controller::class, 'transaction'])->name('lnmo.transaction');
    Route::post('LMNO/transaction/callback', [LNMO_Controller::class, 'callback'])->name('lnmo.transaction.callback');
    Route::post('LMNO/query', [LNMO_Controller::class, 'query'])->name('lnmo.query');
    // MPESA C2B ROUTES
    Route::get('C2B/register', [C2B_Controller::class, 'register'])->name('c2b.register'); // use/hit only once.
    Route::post('C2B/transaction', [C2B_Controller::class, 'transaction'])->name('c2b.transaction'); // only for simulation services.
    Route::post('C2B/validation/callback', [C2B_Controller::class, 'validation'])->name('c2b.validation.callback');
    Route::post('C2B/confirmation/callback', [C2B_Controller::class, 'confirmation'])->name('c2b.confirmation.callback');
    Route::post('C2B/status', [C2B_Controller::class, 'status'])->name('c2b.status');
    Route::post('C2B/status/callback', [C2B_Controller::class, 'statusCallback'])->name('c2b.status.callback');
    Route::post('C2B/reverse', [C2B_Controller::class, 'reverse'])->name('c2b.reverse');
    Route::post('C2B/reverse/callback', [C2B_Controller::class, 'reverseCallback'])->name('c2b.reverse.callback');
    Route::get('C2B/balance', [C2B_Controller::class, 'balance'])->name('c2b.balance');
    Route::post('C2B/balance/callback', [C2B_Controller::class, 'balanceCallback'])->name('c2b.balance.callback');
    // MPESA B2C ROUTES
    Route::post('B2C/transaction', [B2C_Controller::class, 'transaction'])->name('b2c.transaction');
    Route::post('B2C/transaction/callback', [B2C_Controller::class, 'callback'])->name('b2c.transaction.callback');
    Route::post('B2C/status', [B2C_Controller::class, 'status'])->name('b2c.status');
    Route::post('B2C/status/callback', [B2C_Controller::class, 'statusCallback'])->name('b2c.status.callback');
    Route::post('B2C/reverse', [B2C_Controller::class, 'reverse'])->name('b2c.reverse');
    Route::post('B2C/reverse/callback', [B2C_Controller::class, 'reverseCallback'])->name('b2c.reverse.callback');
    Route::get('B2C/balance', [B2C_Controller::class, 'balance'])->name('b2c.balance');
    Route::post('B2C/balance/callback', [B2C_Controller::class, 'balanceCallback'])->name('b2c.balance.callback');
});
