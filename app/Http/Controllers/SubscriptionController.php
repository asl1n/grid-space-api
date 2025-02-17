<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends Controller
{
    /**
     * Create a new subscription (for authenticated users).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createSubscription(Request $request)
    {
        try {
            // Validate the incoming request data
            $validator = Validator::make($request->all(), [
                'fromDate' => 'required|date',
                'toDate' => 'required|date|after:fromDate',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            // Create the subscription for the authenticated user
            $subscription = Auth::user()->subscriptions()->create([
                'fromDate' => $request->fromDate,
                'toDate' => $request->toDate,
            ]);

            return response()->json([
                'message' => 'Subscription created successfully.',
                'subscription' => $subscription,
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Error creating subscription: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while creating the subscription.'], 500);
        }
    }

    /**
     * List all users with active subscriptions.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function listSubscriptionsWithUsers()
    {
        try {

            if (!Auth::user()->hasRole('admin')) {
                return response()->json(['error' => 'Unauthorized: Only admins can view all bookings.'], 403);
            }
            // Retrieve all subscriptions with user details
            $subscriptions = Subscription::with('user')->get(); // Eager load the user relationship

            return response()->json([
                'message' => 'Subscriptions retrieved successfully.',
                'subscriptions' => $subscriptions,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error listing subscriptions: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while retrieving subscriptions.'], 500);
        }
    }
}