<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventory\StoreCotroller;
use App\Http\Controllers\Inventory\StoreHandleCotroller;

Route::middleware([
    'auth:sanctum',
])->prefix('inventory')
->name('inventory.')
->group(function($router){

    Route::controller(StoreCotroller::class)->group(function($router){
        $router->get('/stores', 'index')->name('stores.index');
        $router->get('/stores-bulk-export', 'bulkExports')->name('stores.bulkexports');
        $router->delete('/stores','destroy')->name('stores.destroy');
        $router->get('/stores/data-sync','dataSync')->name('stores.datasync');
    });

    Route::controller(StoreHandleCotroller::class)->group(function($router){
        $router->get('/storehandles', 'index')->name('storehandles.index');
        $router->post('/storehandles', 'store')->name('storehandles.store');
        $router->get('/storehandles/bulk-export', 'bulkExports')->name('storehandles.bulkexports');
        $router->delete('/storehandles','destroy')->name('storehandles.destroy');
        $router->get('/storehandles/drop-down', 'dropdown')->name('storehandles.dropdown');
    });

});

