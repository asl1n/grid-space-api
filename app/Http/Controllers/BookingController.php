<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class BookingController extends Controller
{
    /**
     * Create a new booking (for users with the 'user' role).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createBooking(Request $request)
    {
        try {

            $user = Auth::user();
            // Ensure the user has the 'user' role
            if (!$user->hasRole('user')) {
                return response()->json(['error' => 'Unauthorized: Only users can create bookings.'], 403);
            }

            // Create the booking
            $booking = Booking::create([
                'fromDate' => $request->fromDate,
                'toDate' => $request->toDate,
                'isApproved' => false,
                'userId' => $user->id,
            ]);

            return response()->json([
                'message' => 'Booking created successfully.',
                'booking' => $booking,
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Error creating booking: ' . $e->getMessage());
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * Approve a booking (for users with the 'admin' role).
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function approveBooking($id)
    {
        try {
            // Ensure the user has the 'admin' role
            if (!Auth::user()->hasRole('admin')) {
                return response()->json(['error' => 'Unauthorized: Only admins can approve bookings.'], 403);
            }

            // Find the booking by ID
            $booking = Booking::find($id);

            if (!$booking) {
                return response()->json(['error' => 'Booking not found.'], 404);
            }

            // Update the booking status to approved
            $booking->update(['isApproved' => true]);

            return response()->json([
                'message' => 'Booking approved successfully.',
                'booking' => $booking,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error approving booking: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while approving the booking.'], 500);
        }
    }

    /**
     * List all bookings (for users with the 'admin' role).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function listBookings()
    {
        try {
            // Ensure the user has the 'admin' role
            if (!Auth::user()->hasRole('admin')) {
                return response()->json(['error' => 'Unauthorized: Only admins can view all bookings.'], 403);
            }

            // Retrieve all bookings with user details
            $bookings = Booking::with('user')->get();

            return response()->json([
                'message' => 'Bookings retrieved successfully.',
                'bookings' => $bookings,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error listing bookings: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while retrieving bookings.'], 500);
        }
    }

    /**
     * Delete a booking (for users with the 'admin' role).
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteBooking($id)
    {
        try {
            // Ensure the user has the 'admin' role
            if (!Auth::user()->hasRole('admin')) {
                return response()->json(['error' => 'Unauthorized: Only admins can delete bookings.'], 403);
            }

            // Find the booking by ID
            $booking = Booking::find($id);

            if (!$booking) {
                return response()->json(['error' => 'Booking not found.'], 404);
            }

            // Delete the booking
            $booking->delete();

            return response()->json([
                'message' => 'Booking deleted successfully.',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting booking: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while deleting the booking.'], 500);
        }
    }
}