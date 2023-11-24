<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): JsonResponse
    {
        try {
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user = Auth::user();

                $success['token'] = $user->createToken('bookStoreAdmin')->plainTextToken;
                $success['user'] = $user;
                return $this->sendResponse($success);
            } else {
                return $this->sendError(
                    'Incorrect email or password!',
                    400
                );
            }
        } catch (\Exception $e) {
            return $this->sendError(
                $e->getMessage(),
                500
            );
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): JsonResponse
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return $this->sendResponse(null, 'Logged out successfully!', 204);
        } catch (\Exception $e) {
            return $this->sendError(
                $e->getMessage(),
                500
            );
        }
    }
}
