<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Stores\OrderController;

Route::middleware([
    'auth:sanctum',
])->prefix('stores')
->name('stores.')
->group(function($router){
    $router->get('/orders', [OrderController::class, 'index'])->name('orders.index');
    $router->get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    $router->post('/orders/adjuststock', [OrderController::class, 'adjust_stock'])->name('orders.adjuststock');
    $router->get('/orders/{id}/download', [OrderController::class, 'download'])->name('orders.download');
    $router->get('/orders/export/bulk-action', [OrderController::class, 'bulkExports'])->name('orders.bulkexports');
    $router->put('/orders/exchange-rate/{id}', [OrderController::class, 'exchangeRate'])->name('orders.exchangeRate');
});

