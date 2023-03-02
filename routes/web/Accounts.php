<?php


use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Accounts\RoleController;
use App\Http\Controllers\Accounts\UserController;
use App\Http\Controllers\Accounts\PermissionsController;
use App\Http\Controllers\Accounts\ActivityLoggerController;

Route::middleware([
    'auth:sanctum',
])->prefix('accounts')
    ->name('accounts.')
    ->group(function ($router) {

        Route::controller(ActivityLoggerController::class)->group(function ($router) {
            $router->get('/activity-logs', 'index')->name('activitylogs.index');
            $router->get('/activity-logs/{id}', 'show')->name('activitylogs.show');
            $router->delete('/activity-logs', 'destroy')->name('activitylogs.destroy');
            $router->get('/activity-logs/export/bulk-action', 'bulkExports')->name('activitylogs.bulkexports');
        });

        // users
        Route::controller(UserController::class)->group(function ($router) {
            $router->get('/users', 'index')->name('users.index');
            $router->post('/users/store', 'store')->name('users.store');
            $router->get('/users/{id}', 'show')->name('users.show');
            $router->get('/users/{user}/edit', 'edit')->name('users.edit');
            $router->put('/users/{user}/update', 'UpdateUserProfileInformation')->name('users.UpdateUserProfileInformation');
            $router->put('/users/{user}/update-password', 'updatePassword')->name('users.updatePassword');
            $router->delete('/users', 'destroy')->name('users.destroy');
            $router->get('/users/export/bulk-action', 'bulkExports')->name('users.bulkexports');
        });

        // roles
        Route::controller(RoleController::class)->group(function ($router) {
            $router->get('/roles', 'index')->name('roles.index');
        });

        // permissioms
        Route::controller(PermissionsController::class)->group(function ($router) {
            $router->get('/permissions', 'index')->name('permissions.index');
            $router->post('/permissions/store', 'store')->name('permissions.store');
            $router->put('/permissions/{user}/update', 'update')->name('permissions.update');
            $router->delete('/permissions', 'destroy')->name('permissions.destroy');
        });
    });
