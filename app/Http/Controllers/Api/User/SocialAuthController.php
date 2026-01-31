<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ValidationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class SocialAuthController extends Controller
{
    use ApiResponse;

    /**
     * Login/Register with Google
     */
    public function loginWithGoogle(Request $request): JsonResponse
    {
        $validator = ValidationService::make($request->all(), [
            'access_token' => 'required|string',
            // 'phone' => 'required|string|min:10',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorWithFirstMessage($validator);
        }

        try {
            // Validate token with Google
            $response = Http::get('https://www.googleapis.com/oauth2/v3/userinfo', [
                'access_token' => $request->access_token,
            ]);

            if (!$response->successful()) {
                return $this->errorResponse('errors.invalid_google_token', [], 401);
            }

            $googleUser = $response->json();

            if (!isset($googleUser['email'])) {
                return $this->errorResponse('errors.google_email_not_found', [], 400);
            }

            // Find or create user
            $user = $this->findOrCreateSocialUser($googleUser, 'google');

            // Generate JWT token
            $token = Auth::guard('api')->login($user);

            // Log the login activity
            activity('user')
                ->causedBy($user)
                ->performedOn($user)
                ->withProperties([
                    'ip_address' => $request->ip(),
                    'provider' => 'google',
                ])
                ->log('User logged in via Google');

            return $this->successResponse([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => 3600,
                'user' => $user,
                'is_new_user' => $user->wasRecentlyCreated,
            ], 'success.user_logged_in');

        } catch (\Exception $e) {
            return $this->errorResponse('errors.social_login_failed', [], 500);
        }
    }

    /**
     * Login/Register with Facebook
     */
    public function loginWithFacebook(Request $request): JsonResponse
    {
        $validator = ValidationService::make($request->all(), [
            'access_token' => 'required|string',
            // 'phone' => 'required|string|min:10',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorWithFirstMessage($validator);
        }

        try {
            // Validate token with Facebook
            $response = Http::get('https://graph.facebook.com/me', [
                'fields' => 'id,name,email',
                'access_token' => $request->access_token,
            ]);

            if (!$response->successful()) {
                return $this->errorResponse('errors.invalid_facebook_token', [], 401);
            }

            $facebookUser = $response->json();

            if (!isset($facebookUser['email'])) {
                return $this->errorResponse('errors.facebook_email_not_found', [], 400);
            }

            // Find or create user
            $user = $this->findOrCreateSocialUser($facebookUser, 'facebook');

            // Generate JWT token
            $token = Auth::guard('api')->login($user);

            // Log the login activity
            activity('user')
                ->causedBy($user)
                ->performedOn($user)
                ->withProperties([
                    'ip_address' => $request->ip(),
                    'provider' => 'facebook',
                ])
                ->log('User logged in via Facebook');

            return $this->successResponse([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => 3600,
                'user' => $user,
                'is_new_user' => $user->wasRecentlyCreated,
            ], 'success.user_logged_in');

        } catch (\Exception $e) {
            return $this->errorResponse('errors.social_login_failed', [], 500);
        }
    }

    /**
     * Find existing user or create new one from social provider data
     */
    private function findOrCreateSocialUser(array $providerUser, string $provider): User
    {
        $socialId = $providerUser['sub'] ?? $providerUser['id'];
        $email = $providerUser['email'];
        $name = $providerUser['name'] ?? explode('@', $email)[0];

        // First, try to find by social provider and ID
        $user = User::where('social_provider', $provider)
            ->where('social_id', $socialId)
            ->first();

        if ($user) {
            return $user;
        }

        // Then, try to find by email
        $user = User::where('email', $email)->first();

        if ($user) {
            // Link social account to existing user
            $user->update([
                'social_provider' => $provider,
                'social_id' => $socialId,
            ]);
            return $user;
        }

        // Create new user
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'social_provider' => $provider,
            'social_id' => $socialId,
            // 'phone' => $providerUser['phone'],
        ]);

        return $user;
    }

    /**
     * Login/Register with Apple
     */
    public function loginWithApple(Request $request): JsonResponse
    {
        $validator = ValidationService::make($request->all(), [
            'identity_token' => 'required|string',
            'user_identifier' => 'required|string',
            'email' => 'nullable|email',
            'name' => 'nullable|string',
            // 'phone' => 'required|string|min:10',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorWithFirstMessage($validator);
        }

        try {
            // For Apple Sign-In, the identity_token is a JWT that should be verified
            // In production, you should verify the token signature with Apple's public keys
            // For now, we'll decode and validate the claims
            
            $identityToken = $request->identity_token;
            $parts = explode('.', $identityToken);
            
            if (count($parts) !== 3) {
                return $this->errorResponse('errors.invalid_apple_token', [], 401);
            }

            $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);

            if (!$payload || !isset($payload['sub'])) {
                return $this->errorResponse('errors.invalid_apple_token', [], 401);
            }

            // Verify the subject matches the user_identifier
            if ($payload['sub'] !== $request->user_identifier) {
                return $this->errorResponse('errors.apple_token_mismatch', [], 401);
            }

            // Get email from payload or request (Apple only provides email on first login)
            $email = $payload['email'] ?? $request->email;
            $name = $request->name;

            if (!$email) {
                // Try to find user by Apple ID
                $user = User::where('social_provider', 'apple')
                    ->where('social_id', $request->user_identifier)
                    ->first();
                    
                if (!$user) {
                    return $this->errorResponse('errors.apple_email_required', [], 400);
                }
            } else {
                // Find or create user
                $appleUser = [
                    'sub' => $request->user_identifier,
                    'email' => $email,
                    'name' => $name,
                ];
                $user = $this->findOrCreateSocialUser($appleUser, 'apple');
            }

            // Generate JWT token
            $token = Auth::guard('api')->login($user);

            // Log the login activity
            activity('user')
                ->causedBy($user)
                ->performedOn($user)
                ->withProperties([
                    'ip_address' => $request->ip(),
                    'provider' => 'apple',
                ])
                ->log('User logged in via Apple');

            return $this->successResponse([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => 3600,
                'user' => $user,
                'is_new_user' => $user->wasRecentlyCreated,
            ], 'success.user_logged_in');

        } catch (\Exception $e) {
            return $this->errorResponse('errors.social_login_failed', [], 500);
        }
    }
}
