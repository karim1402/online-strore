<?php

namespace App\Http\Controllers\Api\V2\User;

use App\Http\Controllers\Api\User\AuthController as V1AuthController;

/**
 * V2 AuthController
 *
 * Extends the v1 AuthController so all existing methods work out-of-the-box.
 * Override any method below to add v2-specific logic for that endpoint.
 *
 * Example:
 *   public function login(Request $request): JsonResponse
 *   {
 *       // v2 login logic here
 *   }
 */
class AuthController extends V1AuthController
{
    // All v1 methods (sendOtp, register, login, logout, refresh,
    // profile, updateProfile, changePassword, updateFcmToken,
    // verifyEmail, resendVerificationCode, deleteAccount)
    // are inherited automatically.
    //
    // Add overrides below as needed.
}
