<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Settings\CurrencyController;

Route::middleware([
    'auth:sanctum',
])->prefix('settings')
    ->name('settings.')
    ->group(function () {
        Route::controller(CurrencyController::class)->group(function($router) {
            $router->get('/currencies', 'index')->name('currencies.index');
            $router->get('/currencies/{id}', 'show')->name('currencies.show');
            $router->post('/currencies', 'store')->name('currencies.store');
            $router->put('/currencies/{id}', 'update')->name('currencies.update');
            $router->delete('/currencies', 'destroy')->name('currencies.destroy');
            $router->get('/currencies/export/bulk-action',  'bulkAction')->name('currencies.export.bulkaction');
        });
});
