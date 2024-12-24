<?php

use Illuminate\Support\Facades\Route;
use Reddot\Employee\Management\Http\Controllers\Api\Hr4uEmployeeController;

Route::middleware('auth:api', 'setlocale', 'bindings', 'sanitize')->prefix('api/1.0')->name('api.')->group(function () {
    // HR4U Employee Details
    Route::get('hr4u-employee-details', [Hr4uEmployeeController::class, 'hr4uEmployeeDetails'])->name('employee.details');
});
