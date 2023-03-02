<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Drivers\DropoffRecipeController;


Route::middleware([
    'auth:sanctum',
])->prefix('drivers')
    ->name('drivers.dropoffs.')
    ->group(function () {
        Route::controller(DropoffRecipeController::class)->group(function ($router) {
            $router->get('/dropoffs', 'index')->name('index');
            $router->get('/dropoffs/{id}', 'show')->name('show');
            $router->post('/dropoffs', 'store')->name('store');
            $router->put('/dropoffs/{id}', 'update')->name('update');
            $router->put('/dropoffs/{id}/quick-downdown', 'quickdowndown')->name('quickDowndown');
            $router->get('/dropoffs/export/bulk-action',  'bulkAction')->name('export.bulkaction');
            $router->get('/dropoffs/scanner/recipe_filter',  'recipe_filter')->name('scanner.recipe_filter');
        });
    });
