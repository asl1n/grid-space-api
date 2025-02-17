<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\SubscriptionController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::get('/', function () {
    echo 'Hello world from api';
});

// Protected routes (require authentication)
Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);

    // Booking-related routes
    Route::post('bookings', [BookingController::class, 'createBooking']); // Create a booking (for users with the 'user' role)
    Route::patch('bookings/{id}/approve', [BookingController::class, 'approveBooking']); // Approve a booking (for admins)
    Route::get('bookings', [BookingController::class, 'listBookings']); // List all bookings (for admins)
    Route::delete('bookings/{id}', [BookingController::class, 'deleteBooking']); // Delete a booking (for admins)
  
    Route::post('subscriptions', [SubscriptionController::class, 'createSubscription']);
    Route::get('subscriptions/users', [SubscriptionController::class, 'listSubscriptionsWithUsers']);
});

