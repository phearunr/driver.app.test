<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Stocks\SockReturnTempController;

Route::middleware([
    'auth:sanctum',
])->prefix('stocks')
    ->name('stocks.')
    ->group(function () {
        Route::controller(SockReturnTempController::class)->group(function($router){
            $router->get('/stock-returns-temp','index')->name('stockreturnstemp.index');
            $router->get('/stock-returns-temp/{id}','show')->name('stockreturnstemp.show');
            $router->post('/stock-returns-temp', 'store')->name('stockreturnstemp.store');
            $router->put('/stock-returns-temp', 'update')->name('stockreturnstemp.update');
            $router->delete('/stock-returns-temp', 'destroy')->name('stockreturnstemp.destroy');
            $router->get('/stock-returns-tem-bulk-export',  'bulkExports')->name('stockreturnstemp.bulkexports');
        });
    });

