<?php

use App\Http\Controllers\SystemMetricsController;
use Illuminate\Support\Facades\Route;

Route::get('/',[SystemMetricsController::class , 'Index'])->name('dashboard');
