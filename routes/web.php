<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuUserController;

Route::get('/', [MenuUserController::class, 'index'])->name('welcome');
