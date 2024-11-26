<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservationController;

Route::get('/reservas', [ReservationController::class, 'showReservations'])->name('reservas');
Route::post('/reservas', [ReservationController::class, 'makeReservation'])->name('makeReservation');
