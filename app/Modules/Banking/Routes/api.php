<?php

use App\Modules\Banking\UI\Controllers\BalanceController;
use App\Modules\Banking\UI\Controllers\EventController;
use App\Modules\Banking\UI\Controllers\ResetController;
use Illuminate\Support\Facades\Route;

Route::get('/balance', [BalanceController::class, 'show']);
Route::post('/event', [EventController::class, 'store']);
Route::post('/reset', [ResetController::class, 'reset']);
