<?php

use Illuminate\Support\Facades\Route;
use Montanabay39\Mpesa\Http\Controllers\LNMO_Controller;

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

Route::group(['middleware' => 'api', 'prefix' => 'api/vendor/montanabay39/mpesa/'], function () {
    // MPESA LNMO ROUTES
    Route::post('lmno/transact', [LNMO_Controller::class, 'transact'])->name('mpesa.lnmo.transact');
    Route::post('lmno/callback', [LNMO_Controller::class, 'callback'])->name('mpesa.lnmo.callback');
    Route::post('lmno/query', [LNMO_Controller::class, 'query'])->name('mpesa.lnmo.query');

    // MPESA C2B ROUTES
    /* Route::get('C2B/register', [C2B_Controller::class, 'register'])->name('c2b.register');
    Route::post('C2B/transact', [C2B_Controller::class, 'transact'])->name('c2b.transact');
    Route::post('C2B/validation', [C2B_Controller::class, 'validation'])->name('c2b.validation');
    Route::post('C2B/confirmation', [C2B_Controller::class, 'confirmation'])->name('c2b.confirmation');
    Route::get('C2B/balance', [C2B_Controller::class, 'balance'])->name('c2b.balance');
    Route::post('C2B/balance/callback', [C2B_Controller::class, 'balanceCallback'])->name('c2b.balance.callback');
    Route::post('C2B/status', [C2B_Controller::class, 'status'])->name('c2b.status');
    Route::post('C2B/status/callback', [C2B_Controller::class, 'statusCallback'])->name('c2b.status.callback');
    Route::post('C2B/reverse/transaction', [C2B_Controller::class, 'reverseTransaction'])->name('c2b.reverse.transaction');
    Route::post('C2B/reverse/transaction/callback', [C2B_Controller::class, 'reverseTransactionCallback'])->name('c2b.reverse.transaction.callback'); */

    // MPESA B2C ROUTES
    /* Route::post('B2C/transact', [B2C_Controller::class, 'transact'])->name('b2c.transact');
    Route::post('B2C/callback', [B2C_Controller::class, 'callback'])->name('b2c.callback');
    Route::get('B2C/balance', [B2C_Controller::class, 'balance'])->name('b2c.balance');
    Route::post('B2C/balance/callback', [B2C_Controller::class, 'balanceCallback'])->name('b2c.balance.callback');
    Route::post('B2C/status', [B2C_Controller::class, 'status'])->name('b2c.status');
    Route::post('B2C/status/callback', [B2C_Controller::class, 'statusCallback'])->name('b2c.status.callback');
    Route::post('B2C/reverse/transaction', [B2C_Controller::class, 'reverseTransaction'])->name('b2c.reverse.transaction');
    Route::post('B2C/reverse/transaction/callback', [B2C_Controller::class, 'reverseTransactionCallback'])->name('b2c.reverse.transaction.callback'); */

    /*-------------------
     * 
     * Api resources sits here.
     */
    Route::apiResources([
        // 'lmno' => LNMO_Controller::class
    ]);
});
