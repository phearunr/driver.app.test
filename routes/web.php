<?php

use App\Models\User;
use Inertia\Inertia;
use App\Solid\PdfExport;
use App\Solid\OrderReports;
use App\Solid\OCP\Rectangle;
use App\Solid\OCP\ArearCalutor;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('test-ocp', function () {
    return(new ArearCalutor())->totalArea(
        new Rectangle(10, 20)
    );
});

// Route::get('test-orders', function () {
//     $orders = new OrderReports();
//     $pdf = new PdfExport();
//     return $pdf->export($orders->between('1 jan 2023', '31 jan 2023'));
// });

// $customer = App\Customer::find(1);
// $customer->products()->detach([1,2,3]);
// $customer->products()->attach([1,2,3]);

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

// Route::get('php-info', fn () => phpinfo());

require __DIR__ . '/web/Stores.php';
require __DIR__ . '/web/Reports.php';
require __DIR__ . '/web/Products.php';
require __DIR__ . '/web/Contracts.php';
require __DIR__ . '/web/Drivers.php';
require __DIR__ . '/web/Stocks.php';
require __DIR__ . '/web/Inventory.php';
require __DIR__ . '/web/Accounts.php';
require __DIR__ . '/web/Settings.php';
require __DIR__ . '/web/ChatRooms.php';
