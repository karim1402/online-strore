<?php

namespace App\Http\Controllers\Api\V2\Admin;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    use ApiResponse;

    /**
     * Get all activity logs with pagination and filtering
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Activity::with(['causer', 'subject']);

            // Filter by log name (e.g., admin, user, store, product, etc.)
            if ($request->has('log_name')) {
                $query->where('log_name', $request->log_name); 
            }

            // Filter by event type (created, updated, deleted, etc.)
            if ($request->has('event')) {
                $query->where('event', $request->event);
            }

            // Filter by causer type (Admin, User, Vendor, etc.)
            if ($request->has('causer_type')) {
                $query->where('causer_type', $request->causer_type);
            }

            // Filter by causer ID
            if ($request->has('causer_id')) {
                $query->where('causer_id', $request->causer_id);
            }

            // Filter by subject type (Store, Product, Category, etc.)
            if ($request->has('subject_type')) {
                $query->where('subject_type', $request->subject_type);
            }

            // Filter by subject ID
            if ($request->has('subject_id')) {
                $query->where('subject_id', $request->subject_id);
            }

            // Filter by description (partial match)
            if ($request->has('description')) {
                $query->where('description', 'like', '%' . $request->description . '%');
            }

            // Filter by date range
            if ($request->has('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }

            if ($request->has('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            // Order by latest first
            $query->orderBy('created_at', 'desc');

            // Paginate results
            $perPage = $request->input('per_page', 15);
            $logs = $query->paginate($perPage);

            return $this->successResponse($logs, 'success.logs_fetched');

        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Get a single activity log by ID
     */
    public function show($id): JsonResponse
    {
        try {
            $log = Activity::with(['causer', 'subject'])->findOrFail($id);
            
            return $this->successResponse($log, 'success.log_fetched');

        } catch (\Exception $e) {
            return $this->errorResponse('errors.not_found', [], 404);
        }
    }

    /**
     * Get activity logs for a specific model
     */
    public function forModel(Request $request, string $modelType, int $modelId): JsonResponse
    {
        try {
            $query = Activity::with(['causer', 'subject'])
                ->where('subject_type', $modelType)
                ->where('subject_id', $modelId)
                ->orderBy('created_at', 'desc');

            $perPage = $request->input('per_page', 15);
            $logs = $query->paginate($perPage);

            return $this->successResponse($logs, 'success.logs_fetched');

        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Get activity logs by a specific user (causer)
     */
    public function byUser(Request $request, string $userType, int $userId): JsonResponse
    {
        try {
            $query = Activity::with(['causer', 'subject'])
                ->where('causer_type', $userType)
                ->where('causer_id', $userId)
                ->orderBy('created_at', 'desc');

            $perPage = $request->input('per_page', 15);
            $logs = $query->paginate($perPage);

            return $this->successResponse($logs, 'success.logs_fetched');

        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Get statistics about activity logs
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = [
                'total_activities' => Activity::count(),
                'today_activities' => Activity::whereDate('created_at', today())->count(),
                'this_week_activities' => Activity::whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])->count(),
                'this_month_activities' => Activity::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
                'by_log_name' => Activity::selectRaw('log_name, COUNT(*) as count')
                    ->groupBy('log_name')
                    ->pluck('count', 'log_name'),
                'by_event' => Activity::selectRaw('event, COUNT(*) as count')
                    ->groupBy('event')
                    ->pluck('count', 'event'),
                'recent_activities' => Activity::with(['causer', 'subject'])
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get(),
            ];

            return $this->successResponse($stats, 'success.stats_fetched');

        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Delete old activity logs (optional cleanup endpoint)
     */
    public function cleanup(Request $request): JsonResponse
    {
        try {
            $days = $request->input('days', 365); // Default to 1 year
            
            $deleted = Activity::where('created_at', '<', now()->subDays($days))->delete();

            return $this->successResponse([
                'deleted_count' => $deleted
            ], 'success.logs_cleaned');

        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Get unique log names for filtering
     */
    public function logNames(): JsonResponse
    {
        try {
            $logNames = Activity::distinct()->pluck('log_name');
            
            return $this->successResponse($logNames, 'success.log_names_fetched');

        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Get unique event types for filtering
     */
    public function eventTypes(): JsonResponse
    {
        try {
            $eventTypes = Activity::distinct()->pluck('event')->filter();
            
            return $this->successResponse($eventTypes, 'success.event_types_fetched');

        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }
}
