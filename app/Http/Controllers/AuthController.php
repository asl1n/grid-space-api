<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\DB; // Import the DB facade
use Exception;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    /**
     * Register a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        try {
            // Start a database transaction
            DB::beginTransaction();

            // Validate the incoming request data
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:users',
                'email' => 'required|string|email|max:255|unique:users',
                'phone' => 'nullable|string|max:15',
                'password' => 'required|string|min:6',
            ]);

            // Return validation errors if any
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            // Create the user
            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => bcrypt($request->password),
            ]);

            // Assign the 'user' role to the newly registered user
            $user->assignRole('user');

            // Generate a JWT token for the newly registered user
            $token = auth()->login($user);

            // Commit the transaction
            DB::commit();

            // Return the token, user details, and roles
            return $this->respondWithToken($token);
        } catch (Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollBack();

            // Log the error for debugging purposes
            \Log::error('Error during registration: ' . $e->getMessage());

            // Return a generic error message to the client
            return response()->json([
                'error' => 'An error occurred while registering the user.',
                'message' => $e->getMessage(), // Optional: Include the exception message for debugging
            ], 500);
        }
    }

    /**
     * Login user and create token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        try {
            // Validate the incoming request data
            $credentials = $request->only(['email', 'password']);

            // Attempt to authenticate the user
            if (!$token = auth()->attempt($credentials)) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }

            // Return the token, user details, and roles
            return $this->respondWithToken($token);
        } catch (Exception $e) {
            // Log the error for debugging purposes
            \Log::error('Error during login: ' . $e->getMessage());

            // Return a generic error message to the client
            return response()->json([
                'error' => 'An error occurred while logging in.',
                'message' => $e->getMessage(), // Optional: Include the exception message for debugging
            ], 500);
        }
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        try {
            auth()->logout();
            return response()->json(['message' => 'Successfully logged out']);
        } catch (Exception $e) {
            // Log the error for debugging purposes
            \Log::error('Error during logout: ' . $e->getMessage());

            // Return a generic error message to the client
            return response()->json([
                'error' => 'An error occurred while logging out.',
                'message' => $e->getMessage(), // Optional: Include the exception message for debugging
            ], 500);
        }
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        try {
            return $this->respondWithToken(auth()->refresh());
        } catch (Exception $e) {
            // Log the error for debugging purposes
            \Log::error('Error during token refresh: ' . $e->getMessage());

            // Return a generic error message to the client
            return response()->json([
                'error' => 'An error occurred while refreshing the token.',
                'message' => $e->getMessage(), // Optional: Include the exception message for debugging
            ], 500);
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        try {
            // Get the authenticated user
            $user = auth()->user();

            // Fetch the roles assigned to the user
            $roles = $user->getRoleNames(); // Returns a collection of role names

            // Return the user details along with their roles
            return response()->json([
                'user' => $user,
                'roles' => $roles,
            ]);
        } catch (Exception $e) {
            // Log the error for debugging purposes
            \Log::error('Error fetching user details: ' . $e->getMessage());

            // Return a generic error message to the client
            return response()->json([
                'error' => 'An error occurred while fetching user details.',
                'message' => $e->getMessage(), // Optional: Include the exception message for debugging
            ], 500);
        }
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        // Get the authenticated user
        $user = auth()->user();

        // Fetch the roles assigned to the user
        $roles = $user->getRoleNames(); // Returns a collection of role names

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60, // Token expiration time in seconds
            'user' => $user,
            // 'roles' => $roles, // Include the roles in the response
        ]);
    }
}