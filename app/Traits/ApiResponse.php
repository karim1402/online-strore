<?php

namespace App\Traits;

use App\Services\LocalizationService;
use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Return a success JSON response
     */
    protected function successResponse($data = null, string $messageKey = 'success.operation_successful', array $replace = [], int $statusCode = 200): JsonResponse
    {
        $message = LocalizationService::getMessage($messageKey, $replace);
        
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        } else {
            $response['data'] = null;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Return an error JSON response
     */
    protected function errorResponse(string $messageKey = 'errors.server_error', array $replace = [], int $statusCode = 400, $errors = null): JsonResponse
    {
        $message = LocalizationService::getMessage($messageKey, $replace);
        
        $response = [
            'success' => false,
            'message' => $message,
            'data' => null
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Return a validation error response
     */
    protected function validationErrorResponse($errors, string $messageKey = 'errors.validation_failed'): JsonResponse
    {
        $message = LocalizationService::getMessage($messageKey);
        
        // Format errors if it's a Validator instance
        if ($errors instanceof \Illuminate\Validation\Validator) {
            $formattedErrors = \App\Services\ValidationService::formatErrors($errors);
        } else {
            $formattedErrors = $errors;
        }
        
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => $formattedErrors
        ], 422);
    }

    /**
     * Return a validation error response with first error as message
     */
    protected function validationErrorWithFirstMessage($errors): JsonResponse
    {
        if ($errors instanceof \Illuminate\Validation\Validator) {
            $message = \App\Services\ValidationService::getFirstError($errors);
            $formattedErrors = \App\Services\ValidationService::formatErrors($errors);
        } else {
            $message = LocalizationService::getMessage('errors.validation_failed');
            $formattedErrors = $errors;
        }
        
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => $formattedErrors
        ], 422);
    }

    /**
     * Return an authentication error response
     */
    protected function authErrorResponse(string $messageKey = 'errors.unauthenticated'): JsonResponse
    {
        $message = LocalizationService::getMessage($messageKey);
        
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'error' => 'authentication_required'
        ], 401);
    }

    /**
     * Return an authorization error response
     */
    protected function forbiddenResponse(string $messageKey = 'errors.access_denied'): JsonResponse
    {
        $message = LocalizationService::getMessage($messageKey);
        
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'error' => 'access_denied'
        ], 403);
    }

    /**
     * Return a not found error response
     */
    protected function notFoundResponse(string $messageKey = 'errors.resource_not_found'): JsonResponse
    {
        $message = LocalizationService::getMessage($messageKey);
        
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'error' => 'resource_not_found'
        ], 404);
    }
}
