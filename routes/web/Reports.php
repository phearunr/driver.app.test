<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Reports\RecipeController;
use App\Http\Controllers\Reports\DriverDropoffController;

Route::middleware([
    'auth:sanctum',
])->prefix('reports')
    ->name('reports.')
    ->group(function () {
        Route::controller(RecipeController::class)->group(function ($router) {
            $router->get('/recipes', 'index')->name('recipes.index');
            $router->get('/recipes/{id}', 'show')->name('recipes.show');
            $router->put('/recipes/{id}', 'update')->name('recipes.update');
            $router->put('/recipes/{id}/quick-downdown', 'quickDowndown')->name('recipes.quickDowndown');
            $router->get('/recipes/export/bulk-action',  'bulkAction')->name('recipes.export.bulkaction');
        });
    });
