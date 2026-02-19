<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    use ApiResponse;

    /**
     * 1. Dashboard KPI Statistics
     * GET /admin/dashboard/stats
     */
    public function stats(): JsonResponse
    {
        try {
            $now = Carbon::now();
            $startOfThisMonth = $now->copy()->startOfMonth();
            $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
            $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();

            // Total Revenue
            $revenueThisMonth = (float) Order::where('simple_status', 'delivered')
                ->whereBetween('created_at', [$startOfThisMonth, $now])
                ->sum('total');

            $revenueLastMonth = (float) Order::where('simple_status', 'delivered')
                ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
                ->sum('total');

            $totalRevenue = (float) Order::where('simple_status', 'delivered')->sum('total');

            // Orders
            $ordersThisMonth = Order::whereBetween('created_at', [$startOfThisMonth, $now])->count();
            $ordersLastMonth = Order::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();
            $totalOrders = Order::count();

            // Products
            $totalProducts = Product::where('is_active', true)->count();
            $productsLastMonth = Product::where('is_active', true)
                ->where('created_at', '<=', $endOfLastMonth)
                ->count();

            // Active Users
            $totalUsers = User::count();
            $usersSinceLastHour = User::where('created_at', '>=', $now->copy()->subHour())->count();

            // Calculate changes
            $revenueChange = $this->percentageChange($revenueThisMonth, $revenueLastMonth);
            $ordersChange = $this->percentageChange($ordersThisMonth, $ordersLastMonth);
            $productsChange = $this->percentageChange($totalProducts, $productsLastMonth);

            return $this->successResponse([
                'total_revenue' => [
                    'value' => round($totalRevenue, 2),
                    'currency' => 'EGP',
                    'change' => $revenueChange,
                    'trend' => $this->trend($revenueChange),
                ],
                'total_orders' => [
                    'value' => $totalOrders,
                    'change' => $ordersChange,
                    'trend' => $this->trend($ordersChange),
                ],
                'total_products' => [
                    'value' => $totalProducts,
                    'change' => $productsChange,
                    'trend' => $this->trend($productsChange),
                ],
                'active_users' => [
                    'value' => $totalUsers,
                    'change' => $usersSinceLastHour,
                    'trend' => $usersSinceLastHour > 0 ? 'up' : 'neutral',
                    'period' => 'last_hour',
                ],
            ], 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * 2. Order Distribution
     * GET /admin/dashboard/orders-distribution
     */
    public function ordersDistribution(Request $request): JsonResponse
    {
        try {
            $query = Order::query();
            $this->applyPeriodFilter($query, $request);

            $statusMap = [
                'in_progress'   => ['label' => 'Pending',          'color' => '#fbbf24'],
                'ready_to_pick' => ['label' => 'Processing',       'color' => '#8b5cf6'],
                'in_delivery'   => ['label' => 'Out for Delivery', 'color' => '#f97316'],
                'delivered'     => ['label' => 'Delivered',        'color' => '#10b981'],
                'cancelled'     => ['label' => 'Canceled',         'color' => '#ef4444'],
            ];

            $counts = $query->select('simple_status', DB::raw('COUNT(*) as count'))
                ->groupBy('simple_status')
                ->pluck('count', 'simple_status')
                ->toArray();

            $result = [];
            foreach ($statusMap as $status => $meta) {
                $result[] = [
                    'status' => $meta['label'],
                    'count'  => $counts[$status] ?? 0,
                    'color'  => $meta['color'],
                ];
            }

            return $this->successResponse($result, 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * 3. Revenue & Orders Over Time
     * GET /admin/dashboard/revenue-chart
     */
    public function revenueChart(Request $request): JsonResponse
    {
        try {
            $period = $request->input('period', 'week');
            $now = Carbon::now();

            switch ($period) {
                case 'month':
                    $startDate = $now->copy()->subDays(30)->startOfDay();
                    break;
                case 'year':
                    $startDate = $now->copy()->subYear()->startOfDay();
                    break;
                case 'week':
                default:
                    $startDate = $now->copy()->subDays(7)->startOfDay();
                    break;
            }

            // Group by day for week/month, by month for year
            if ($period === 'year') {
                $rawData = Order::where('simple_status', 'delivered')
                    ->where('created_at', '>=', $startDate)
                    ->select(
                        DB::raw("DATE_FORMAT(created_at, '%Y-%m') as date"),
                        DB::raw('SUM(total) as revenue'),
                        DB::raw('COUNT(*) as orders')
                    )
                    ->groupBy('date')
                    ->orderBy('date', 'asc')
                    ->get()
                    ->keyBy('date');

                $result = [];
                $current = $startDate->copy()->startOfMonth();
                while ($current->lte($now)) {
                    $key = $current->format('Y-m');
                    $result[] = [
                        'date'    => $key,
                        'revenue' => isset($rawData[$key]) ? round((float) $rawData[$key]->revenue, 2) : 0,
                        'orders'  => isset($rawData[$key]) ? (int) $rawData[$key]->orders : 0,
                    ];
                    $current->addMonth();
                }
            } else {
                $rawData = Order::where('simple_status', 'delivered')
                    ->where('created_at', '>=', $startDate)
                    ->select(
                        DB::raw('DATE(created_at) as date'),
                        DB::raw('SUM(total) as revenue'),
                        DB::raw('COUNT(*) as orders')
                    )
                    ->groupBy('date')
                    ->orderBy('date', 'asc')
                    ->get()
                    ->keyBy('date');

                $days = $period === 'month' ? 30 : 7;
                $result = [];
                for ($i = $days; $i >= 0; $i--) {
                    $date = $now->copy()->subDays($i)->format('Y-m-d');
                    $result[] = [
                        'date'    => $date,
                        'revenue' => isset($rawData[$date]) ? round((float) $rawData[$date]->revenue, 2) : 0,
                        'orders'  => isset($rawData[$date]) ? (int) $rawData[$date]->orders : 0,
                    ];
                }
            }

            return $this->successResponse($result, 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * 4. Module Performance
     * GET /admin/dashboard/module-performance
     */
    public function modulePerformance(Request $request): JsonResponse
    {
        try {
            $period = $request->input('period', 'month');
            $now = Carbon::now();

            switch ($period) {
                case 'week':
                    $startDate = $now->copy()->subWeek()->startOfDay();
                    break;
                case 'year':
                    $startDate = $now->copy()->subYear()->startOfDay();
                    break;
                case 'month':
                default:
                    $startDate = $now->copy()->subMonth()->startOfDay();
                    break;
            }

            // Join through order_items → products → categories → modules
            $data = DB::table('orders')
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->join('modules', 'categories.module_id', '=', 'modules.id')
                ->where('orders.simple_status', 'delivered')
                ->where('orders.created_at', '>=', $startDate)
                ->whereNull('orders.deleted_at')
                ->select(
                    'modules.name_en as module',
                    DB::raw('COUNT(DISTINCT orders.id) as count'),
                    DB::raw('SUM(order_items.total_price) as revenue')
                )
                ->groupBy('modules.id', 'modules.name_en')
                ->orderBy('revenue', 'desc')
                ->get()
                ->map(function ($item) {
                    return [
                        'module'  => $item->module,
                        'count'   => (int) $item->count,
                        'revenue' => round((float) $item->revenue, 2),
                    ];
                });

            return $this->successResponse($data, 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * 5. Recent Orders
     * GET /admin/dashboard/recent-orders
     */
    public function recentOrders(Request $request): JsonResponse
    {
        try {
            $limit = min((int) $request->input('limit', 5), 50);

            $orders = Order::with('user:id,name')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($order) {
                    return [
                        'id'           => $order->id,
                        'order_number' => '#' . $order->order_number,
                        'customer'     => [
                            'name'   => $order->user->name ?? 'Guest',
                            'avatar' => null,
                        ],
                        'amount'       => round((float) $order->total, 2),
                        'currency'     => 'EGP',
                        'status'       => $order->simple_status,
                        'created_at'   => $order->created_at->toIso8601String(),
                        'time_ago'     => $order->created_at->diffForHumans(),
                    ];
                });

            return $this->successResponse($orders, 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', ['error' => $e->getMessage()], 500);
        }
    }

    // ─── Helpers ──────────────────────────────────────────

    private function percentageChange($current, $previous): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100.0 : 0.0;
        }
        return round((($current - $previous) / $previous) * 100, 1);
    }

    private function trend(float $change): string
    {
        if ($change > 0) return 'up';
        if ($change < 0) return 'down';
        return 'neutral';
    }

    private function applyPeriodFilter($query, Request $request): void
    {
        $period = $request->input('period', 'week');
        $now = Carbon::now();

        switch ($period) {
            case 'today':
                $query->whereDate('created_at', $now->toDateString());
                break;
            case 'week':
                $query->where('created_at', '>=', $now->copy()->subWeek()->startOfDay());
                break;
            case 'month':
                $query->where('created_at', '>=', $now->copy()->subMonth()->startOfDay());
                break;
            case 'year':
                $query->where('created_at', '>=', $now->copy()->subYear()->startOfDay());
                break;
            case 'custom':
                if ($request->filled('start_date')) {
                    $query->where('created_at', '>=', Carbon::parse($request->input('start_date'))->startOfDay());
                }
                if ($request->filled('end_date')) {
                    $query->where('created_at', '<=', Carbon::parse($request->input('end_date'))->endOfDay());
                }
                break;
        }
    }
}
