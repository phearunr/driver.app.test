<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Contracts\ContractController;

Route::middleware([
    'auth:sanctum',
])->prefix('contracts')
    ->name('contracts.')
    ->group(function () {
        Route::controller(ContractController::class)->group(function ($router) {
            $router->get('/suppliers', 'index')->name('suppliers.index');
            $router->get('/customers ', 'index')->name('customers.index');
            $router->get('/customer-groups  ', 'index')->name('customergroups.index');
        });
    });
