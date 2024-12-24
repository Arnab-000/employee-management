<?php

use Illuminate\Support\Facades\Route;
use Reddot\Employee\Management\Http\Controllers\ExampleController;

Route::get('/employee-management', [ExampleController::class, 'index']);
