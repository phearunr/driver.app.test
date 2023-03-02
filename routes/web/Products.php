<?php

use App\Http\Controllers\Products\ProductController;
use Illuminate\Support\Facades\Route;


Route::middleware([
    'auth:sanctum',
])->prefix('products')
    ->name('products.')
    ->group(function () {
        Route::controller(ProductController::class)->group(function ($router) {
            $router->get('/products', 'index')->name('products.index');
            $router->get('/variations', 'index')->name('variations.index');
            $router->get('/categories', 'index')->name('categories.index');
            $router->get('/selling-price-group', 'index')->name('sellingpricegroup.index');
            $router->get('/brands', 'index')->name('brands.index');
        });
    });
