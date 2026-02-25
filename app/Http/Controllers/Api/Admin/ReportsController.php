<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Store;
use App\Models\Delivery;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use App\Models\Payment;
use Spatie\Activitylog\Models\Activity;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    use ApiResponse;

    // ──────────────────────────────────────────
    // 1. SALES & REVENUE REPORTS
    // ──────────────────────────────────────────

    public function totalRevenue(Request $request): JsonResponse
    {
        $query = Order::where('simple_status', 'delivered');
        $this->applyPeriodFilter($query, $request);

        $metrics = $query->selectRaw('
            SUM(total) as gross_revenue,
            SUM(subtotal) as net_revenue,
            SUM(delivery_fee) as delivery_fees,
            SUM(tax) as taxes,
            SUM(discount) as discounts
        ')->first();

        return $this->successResponse($metrics, 'success.data_retrieved');
    }

    public function revenueOverTime(Request $request): JsonResponse
    {
        $query = Order::where('simple_status', 'delivered');
        $this->applyPeriodFilter($query, $request);

        $groupBy = $this->getGroupByFormat($request);

        $data = $query->select(
            DB::raw("DATE_FORMAT(created_at, '{$groupBy}') as period"),
            DB::raw('SUM(total) as revenue'),
            DB::raw('COUNT(id) as order_count')
        )
        ->groupBy('period')
        ->orderBy('period', 'asc')
        ->get();

        return $this->successResponse($data, 'success.data_retrieved');
    }

    public function revenueByModule(Request $request): JsonResponse
    {
        $query = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('modules', 'categories.module_id', '=', 'modules.id')
            ->where('orders.simple_status', 'delivered')
            ->whereNull('orders.deleted_at');

        $this->applyQueryFilter($query, $request, 'orders');

        $rows = $query->select(
            'modules.name_en as module',
            DB::raw('COUNT(DISTINCT orders.id) as orders'),
            DB::raw('SUM(order_items.total_price) as revenue')
        )
        ->groupBy('modules.id', 'modules.name_en')
        ->orderBy('revenue', 'desc')
        ->get();

        $totalRevenue = $rows->sum('revenue');

        $data = $rows->map(function ($row) use ($totalRevenue) {
            return [
                'module'     => strtolower(str_replace(' ', '_', $row->module)),
                'label'      => $row->module,
                'orders'     => (int) $row->orders,
                'revenue'    => round((float) $row->revenue, 2),
                'percentage' => $totalRevenue > 0 ? round(((float) $row->revenue / $totalRevenue) * 100, 1) : 0,
            ];
        });

        return $this->successResponse($data, 'success.data_retrieved');
    }

    public function revenueByCategory(Request $request): JsonResponse
    {
        $query = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('orders.simple_status', 'delivered')
            ->whereNull('orders.deleted_at');

        $this->applyQueryFilter($query, $request, 'orders');

        $data = $query->select(
            'categories.name_en as category',
            DB::raw('COUNT(DISTINCT orders.id) as order_count'),
            DB::raw('SUM(order_items.total_price) as revenue')
        )
        ->groupBy('categories.id', 'categories.name_en')
        ->orderBy('revenue', 'desc')
        ->get();

        return $this->successResponse($data, 'success.data_retrieved');
    }

    public function revenueByStore(Request $request): JsonResponse
    {
        $query = Order::where('simple_status', 'delivered')->with('store:id,name_en');
        $this->applyPeriodFilter($query, $request);

        $data = $query->select('store_id', DB::raw('SUM(total) as revenue'), DB::raw('COUNT(id) as order_count'))
            ->groupBy('store_id')
            ->orderBy('revenue', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'store_id' => $item->store_id,
                    'store_name' => collect($item->store)->get('name_en', 'Unknown'),
                    'revenue' => (float) $item->revenue,
                    'order_count' => (int) $item->order_count,
                ];
            });

        return $this->successResponse($data, 'success.data_retrieved');
    }

    public function revenueByPaymentMethod(Request $request): JsonResponse
    {
        $query = Order::where('simple_status', 'delivered');
        $this->applyPeriodFilter($query, $request);

        $data = $query->select('payment_method', DB::raw('SUM(total) as revenue'), DB::raw('COUNT(id) as order_count'))
            ->groupBy('payment_method')
            ->get();

        return $this->successResponse($data, 'success.data_retrieved');
    }

    public function discountImpact(Request $request): JsonResponse
    {
        $query = Order::where('simple_status', 'delivered');
        $this->applyPeriodFilter($query, $request);

        $data = $query->selectRaw('
            COUNT(id) as total_orders,
            SUM(CASE WHEN discount > 0 THEN 1 ELSE 0 END) as discounted_orders_count,
            SUM(discount) as total_discounts_given,
            SUM(total) as net_revenue,
            SUM(total + discount) as gross_revenue_before_discounts
        ')->first();

        // Avoid division by zero
        $grossRevenue = (float)$data->gross_revenue_before_discounts;
        $discountRatio = $grossRevenue > 0 ? ((float)$data->total_discounts_given / $grossRevenue) * 100 : 0;

        $response = array_merge((array)$data, ['discount_ratio_percentage' => round($discountRatio, 2)]);
        return $this->successResponse($response, 'success.data_retrieved');
    }


    // ──────────────────────────────────────────
    // 1b. SALES CHART ENDPOINTS
    // ──────────────────────────────────────────

    public function revenueTrend(Request $request): JsonResponse
    {
        $query = Order::where('simple_status', 'delivered');

        // Apply start_date / end_date filters
        if ($request->filled('start_date')) {
            $query->where('created_at', '>=', Carbon::parse($request->input('start_date'))->startOfDay());
        }
        if ($request->filled('end_date')) {
            $query->where('created_at', '<=', Carbon::parse($request->input('end_date'))->endOfDay());
        }

        $period = $request->input('period', 'day');
        switch ($period) {
            case 'week':
                $format = '%x-W%v';   // ISO year-week
                break;
            case 'month':
                $format = '%Y-%m';
                break;
            case 'year':
                $format = '%Y';
                break;
            default: // day
                $format = '%Y-%m-%d';
                break;
        }

        $data = $query->select(
            DB::raw("DATE_FORMAT(created_at, '{$format}') as date"),
            DB::raw('SUM(total) as gross_revenue'),
            DB::raw('SUM(subtotal) as net_revenue')
        )
        ->groupBy('date')
        ->orderBy('date', 'asc')
        ->get()
        ->map(function ($row) {
            return [
                'date'          => $row->date,
                'gross_revenue' => round((float) $row->gross_revenue, 2),
                'net_revenue'   => round((float) $row->net_revenue, 2),
            ];
        });

        return $this->successResponse($data, 'success.data_retrieved');
    }

    public function paymentMethodsBreakdown(Request $request): JsonResponse
    {
        $query = Order::where('simple_status', 'delivered');

        if ($request->filled('start_date')) {
            $query->where('created_at', '>=', Carbon::parse($request->input('start_date'))->startOfDay());
        }
        if ($request->filled('end_date')) {
            $query->where('created_at', '<=', Carbon::parse($request->input('end_date'))->endOfDay());
        }

        $rows = $query->select('payment_method', DB::raw('COUNT(id) as count'))
            ->groupBy('payment_method')
            ->get();

        $total = $rows->sum('count');

        $labels = [
            'cash'   => 'Cash on Delivery',
            'online' => 'Online Payment',
        ];

        $data = $rows->map(function ($row) use ($total, $labels) {
            return [
                'method'     => $row->payment_method,
                'label'      => $labels[$row->payment_method] ?? ucfirst($row->payment_method),
                'count'      => (int) $row->count,
                'percentage' => $total > 0 ? round(((int) $row->count / $total) * 100, 1) : 0,
            ];
        });

        return $this->successResponse($data, 'success.data_retrieved');
    }


    // ──────────────────────────────────────────
    // 2. ORDER REPORTS
    // ──────────────────────────────────────────

    public function orderDistribution(Request $request): JsonResponse
    {
        $query = Order::query();
        $this->applyPeriodFilter($query, $request);

        $rows = $query->select('simple_status', DB::raw('COUNT(id) as count'))
            ->groupBy('simple_status')
            ->get();

        $total = $rows->sum('count');

        $statusLabels = [
            'in_progress'   => 'In Progress',
            'ready_to_pick' => 'Ready to Pick',
            'in_delivery'   => 'Out for Delivery',
            'delivered'     => 'Completed',
            'cancelled'     => 'Cancelled',
        ];

        $statuses = $rows->map(function ($row) use ($total, $statusLabels) {
            return [
                'status'     => $row->simple_status,
                'label'      => $statusLabels[$row->simple_status] ?? ucfirst(str_replace('_', ' ', $row->simple_status)),
                'count'      => (int) $row->count,
                'percentage' => $total > 0 ? round(((int) $row->count / $total) * 100, 1) : 0,
            ];
        });

        return $this->successResponse([
            'total'    => $total,
            'statuses' => $statuses,
        ], 'success.data_retrieved');
    }

    public function orderCancellations(Request $request): JsonResponse
    {
        $query = Order::where('simple_status', 'cancelled');
        $this->applyPeriodFilter($query, $request);

        $totalCancelledValue = $query->sum('total');
        $cancellationCount = $query->count();

        // Get logs for cancellation reasons
        $logs = Activity::where('log_name', 'order')
            ->where('description', 'Order cancelled')
            ->whereIn('subject_id', $query->pluck('id'))
            ->get();

        $reasons = [];
        foreach ($logs as $log) {
            $reason = $log->properties['reason'] ?? 'Unknown';
            $reasons[$reason] = ($reasons[$reason] ?? 0) + 1;
        }

        return $this->successResponse([
            'cancellation_count' => $cancellationCount,
            'lost_revenue' => $totalCancelledValue,
            'reasons_breakdown' => $reasons,
        ], 'success.data_retrieved');
    }

    public function averageOrderValue(Request $request): JsonResponse
    {
        $query = Order::where('simple_status', 'delivered');
        $this->applyPeriodFilter($query, $request);

        $data = $query->selectRaw('
            AVG(total) as avg_order_value,
            MIN(total) as min_order_value,
            MAX(total) as max_order_value
        ')->first();

        return $this->successResponse($data, 'success.data_retrieved');
    }

    public function adminCreatedOrders(Request $request): JsonResponse
    {
        $query = Order::whereNull('user_id');
        $this->applyPeriodFilter($query, $request);

        $metrics = $query->selectRaw('
            COUNT(id) as order_count,
            SUM(total) as total_revenue
        ')->first();

        $orders = $query->with('store:id,name_en')->orderBy('created_at', 'desc')->limit(50)->get();

        return $this->successResponse([
            'metrics' => $metrics,
            'recent_orders' => $orders
        ], 'success.data_retrieved');
    }


    // ──────────────────────────────────────────
    // 3. PRODUCT REPORTS
    // ──────────────────────────────────────────

    public function topSellingProducts(Request $request): JsonResponse
    {
        $query = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.simple_status', 'delivered')
            ->whereNull('orders.deleted_at');

        $this->applyQueryFilter($query, $request, 'orders');

        $data = $query->select(
            'products.id',
            'products.name_en',
            DB::raw('SUM(order_items.quantity) as units_sold'),
            DB::raw('SUM(order_items.total_price) as revenue')
        )
        ->groupBy('products.id', 'products.name_en')
        ->orderBy($request->input('sort_by', 'units_sold'), 'desc')
        ->limit($request->input('limit', 50))
        ->get();

        return $this->successResponse($data, 'success.data_retrieved');
    }

    public function mostViewedProducts(): JsonResponse
    {
        $data = Product::orderBy('view_count', 'desc')
            ->select('id', 'name_en', 'view_count', 'is_active')
            ->limit(50)
            ->get();

        return $this->successResponse($data, 'success.data_retrieved');
    }

    public function bestSellersFlagged(): JsonResponse
    {
        $data = Product::where('is_best_seller', true)
            ->with('category:id,name_en')
            ->select('id', 'name_en', 'category_id', 'base_price', 'offer_price')
            ->get();

        return $this->successResponse($data, 'success.data_retrieved');
    }

    public function productsWithOffers(): JsonResponse
    {
        $data = Product::whereNotNull('offer_price')
            ->where('is_active', true)
            ->select('id', 'name_en', 'base_price', 'offer_price')
            ->get()
            ->map(function ($product) {
                $discount = $product->base_price > 0 
                    ? (($product->base_price - $product->offer_price) / $product->base_price) * 100 
                    : 0;
                $product->discount_percentage = round($discount, 2);
                return $product;
            });

        return $this->successResponse($data, 'success.data_retrieved');
    }


    // ──────────────────────────────────────────
    // 4. USER & CUSTOMER REPORTS
    // ──────────────────────────────────────────

    public function userGrowth(Request $request): JsonResponse
    {
        $query = User::query();
        $this->applyPeriodFilter($query, $request);
        $groupBy = $this->getGroupByFormat($request);

        $data = $query->select(
            DB::raw("DATE_FORMAT(created_at, '{$groupBy}') as period"),
            DB::raw('COUNT(id) as registrations')
        )
        ->groupBy('period')
        ->orderBy('period', 'asc')
        ->get();

        return $this->successResponse($data, 'success.data_retrieved');
    }

    public function topCustomers(Request $request): JsonResponse
    {
        $query = DB::table('users')
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->where('orders.simple_status', 'delivered')
            ->whereNull('orders.deleted_at')
            ->whereNull('users.deleted_at');

        $this->applyQueryFilter($query, $request, 'orders');

        $data = $query->select(
            'users.id',
            'users.name',
            'users.email',
            'users.phone',
            DB::raw('COUNT(orders.id) as order_count'),
            DB::raw('SUM(orders.total) as total_spend')
        )
        ->groupBy('users.id', 'users.name', 'users.email', 'users.phone')
        ->orderBy('total_spend', 'desc')
        ->limit($request->input('limit', 50))
        ->get();

        return $this->successResponse($data, 'success.data_retrieved');
    }

    public function inactiveUsers(Request $request): JsonResponse
    {
        $days = $request->input('days_inactive', 30);
        $cutoffDate = Carbon::now()->subDays($days);

        // Users who registered but never placed an order OR haven't placed an order recently
        $data = User::whereDoesntHave('orders', function ($q) use ($cutoffDate) {
                $q->where('created_at', '>=', $cutoffDate);
            })
            ->select('id', 'name', 'email', 'created_at')
            ->paginate($request->input('per_page', 50));

        return $this->successResponse($data, 'success.data_retrieved');
    }

    // ──────────────────────────────────────────
    // 5. DELIVERY REPORTS
    // ──────────────────────────────────────────

    public function driverPerformance(Request $request): JsonResponse
    {
        $query = DB::table('deliveries')
            ->leftJoin('orders', 'deliveries.id', '=', 'orders.delivery_id')
            ->whereNull('deliveries.deleted_at');

        $this->applyQueryFilter($query, $request, 'orders');

        $data = $query->select(
            'deliveries.id',
            'deliveries.name',
            'deliveries.vehicle_type',
            DB::raw('SUM(CASE WHEN orders.simple_status = "delivered" THEN 1 ELSE 0 END) as successful_deliveries'),
            DB::raw('SUM(CASE WHEN orders.simple_status IN ("cancelled", "failed") THEN 1 ELSE 0 END) as failed_deliveries')
        )
        ->groupBy('deliveries.id', 'deliveries.name', 'deliveries.vehicle_type')
        ->orderBy('successful_deliveries', 'desc')
        ->get();

        return $this->successResponse($data, 'success.data_retrieved');
    }

    public function driverAvailability(): JsonResponse
    {
        $data = Delivery::select('status', 'availability', DB::raw('COUNT(*) as count'))
            ->groupBy('status', 'availability')
            ->get();

        return $this->successResponse($data, 'success.data_retrieved');
    }

    public function deliveriesPerDay(Request $request): JsonResponse
    {
        $query = Order::where('simple_status', 'delivered')
            ->where('is_delivery', true);

        if ($request->filled('start_date')) {
            $query->where('created_at', '>=', Carbon::parse($request->input('start_date'))->startOfDay());
        }
        if ($request->filled('end_date')) {
            $query->where('created_at', '<=', Carbon::parse($request->input('end_date'))->endOfDay());
        }

        $data = $query->select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(id) as deliveries')
        )
        ->groupBy('date')
        ->orderBy('date', 'asc')
        ->get()
        ->map(function ($row) {
            $carbonDate = Carbon::parse($row->date);
            return [
                'date'                      => $row->date,
                'day_label'                 => $carbonDate->format('D'),
                'deliveries'                => (int) $row->deliveries,
                'avg_delivery_time_minutes' => null, // No delivered_at timestamp available
            ];
        });

        return $this->successResponse($data, 'success.data_retrieved');
    }


    // ──────────────────────────────────────────
    // 6. VOUCHER REPORTS
    // ──────────────────────────────────────────

    public function voucherUsageAndEffectiveness(Request $request): JsonResponse
    {
        $query = Voucher::withCount('usages')
            ->withSum('usages', 'discount_amount')
            ->orderBy('usages_count', 'desc');

        if ($request->has('status')) {
            $query->where('is_active', filter_var($request->status, FILTER_VALIDATE_BOOLEAN));
        }

        $vouchers = $query->get()->map(function($voucher) {
            return [
                'id' => $voucher->id,
                'code' => $voucher->code,
                'type' => $voucher->type,
                'value' => $voucher->value,
                'is_active' => $voucher->is_active,
                'usage_count' => $voucher->usages_count,
                'total_discount_given' => (float)$voucher->usages_sum_discount_amount,
                'limit' => $voucher->usage_limit,
                'utilization_percentage' => $voucher->usage_limit ? round(($voucher->usages_count / $voucher->usage_limit) * 100, 2) : null
            ];
        });

        return $this->successResponse($vouchers, 'success.data_retrieved');
    }


    // ──────────────────────────────────────────
    // 7. PAYMENT REPORTS
    // ──────────────────────────────────────────

    public function paymentSuccessRate(Request $request): JsonResponse
    {
        $query = Payment::query();
        $this->applyPeriodFilter($query, $request, 'payment_created_at');

        $data = $query->selectRaw('
            COUNT(id) as total_attempts,
            SUM(CASE WHEN success = 1 THEN 1 ELSE 0 END) as successful_payments,
            SUM(CASE WHEN success = 0 THEN 1 ELSE 0 END) as failed_payments,
            SUM(CASE WHEN success = 1 THEN amount_cents ELSE 0 END) / 100 as total_collected
        ')->first();

        $totalAttempts = (int) $data->total_attempts;
        $successfulPayments = (int) $data->successful_payments;
        $failedPayments = (int) $data->failed_payments;
        $totalCollected = round((float) $data->total_collected, 2);
        $successRate = $totalAttempts > 0 ? round(($successfulPayments / $totalAttempts) * 100, 2) : 0;

        return $this->successResponse([
            'success_rate_percentage' => $successRate,
            'total_attempts'         => $totalAttempts,
            'successful_payments'    => $successfulPayments,
            'failed_payments'        => $failedPayments,
            'total_collected'        => $totalCollected,
        ], 'success.data_retrieved');
    }

    public function merchantFees(Request $request): JsonResponse
    {
        $query = Payment::where('success', true);
        $this->applyPeriodFilter($query, $request, 'payment_created_at');

        $data = $query->selectRaw('
            SUM(amount_cents) / 100 as total_processed,
            SUM(merchant_commission) as total_commission,
            SUM(accept_fees) as total_gateway_fees
        ')->first();

        return $this->successResponse([
            'total_processed'    => round((float) ($data->total_processed ?? 0), 2),
            'total_commission'   => round((float) ($data->total_commission ?? 0), 2),
            'total_gateway_fees' => round((float) ($data->total_gateway_fees ?? 0), 2),
        ], 'success.data_retrieved');
    }

    public function monthlyPaymentTrend(Request $request): JsonResponse
    {
        $query = Payment::query();

        if ($request->filled('start_date')) {
            $query->where('payment_created_at', '>=', Carbon::parse($request->input('start_date'))->startOfDay());
        }
        if ($request->filled('end_date')) {
            $query->where('payment_created_at', '<=', Carbon::parse($request->input('end_date'))->endOfDay());
        }

        $data = $query->select(
            DB::raw("DATE_FORMAT(payment_created_at, '%Y-%m') as month"),
            DB::raw("DATE_FORMAT(payment_created_at, '%b') as month_label"),
            DB::raw('SUM(CASE WHEN success = 1 THEN 1 ELSE 0 END) as successful'),
            DB::raw('SUM(CASE WHEN success = 0 THEN 1 ELSE 0 END) as failed'),
            DB::raw('COUNT(id) as total')
        )
        ->groupBy('month', 'month_label')
        ->orderBy('month', 'asc')
        ->get()
        ->map(function ($row) {
            return [
                'month'       => $row->month,
                'month_label' => $row->month_label,
                'successful'  => (int) $row->successful,
                'failed'      => (int) $row->failed,
                'total'       => (int) $row->total,
            ];
        });

        return $this->successResponse($data, 'success.data_retrieved');
    }


    // ──────────────────────────────────────────
    // 8. STORE & VENDOR REPORTS
    // ──────────────────────────────────────────

    public function storeStatusOverview(): JsonResponse
    {
        $data = Store::select('status', DB::raw('COUNT(id) as count'))
            ->groupBy('status')
            ->get();

        return $this->successResponse($data, 'success.data_retrieved');
    }


    // ──────────────────────────────────────────
    // HELPERS
    // ──────────────────────────────────────────

    private function applyPeriodFilter($query, Request $request, $column = 'created_at'): void
    {
        $period = $request->input('period', 'all');
        $now = Carbon::now();

        switch ($period) {
            case 'today':
                $query->whereDate($column, $now->toDateString());
                break;
            case 'week':
                $query->where($column, '>=', $now->copy()->subWeek()->startOfDay());
                break;
            case 'month':
                $query->where($column, '>=', $now->copy()->subMonth()->startOfDay());
                break;
            case 'year':
                $query->where($column, '>=', $now->copy()->subYear()->startOfDay());
                break;
            case 'custom':
                if ($request->filled('start_date')) {
                    $query->where($column, '>=', Carbon::parse($request->input('start_date'))->startOfDay());
                }
                if ($request->filled('end_date')) {
                    $query->where($column, '<=', Carbon::parse($request->input('end_date'))->endOfDay());
                }
                break;
        }
    }

    private function applyQueryFilter($query, Request $request, $tablePrefix): void
    {
        $period = $request->input('period', 'all');
        $now = Carbon::now();
        $column = $tablePrefix . '.created_at';

        switch ($period) {
            case 'today':
                $query->whereDate($column, $now->toDateString());
                break;
            case 'week':
                $query->where($column, '>=', $now->copy()->subWeek()->startOfDay());
                break;
            case 'month':
                $query->where($column, '>=', $now->copy()->subMonth()->startOfDay());
                break;
            case 'year':
                $query->where($column, '>=', $now->copy()->subYear()->startOfDay());
                break;
            case 'custom':
                if ($request->filled('start_date')) {
                    $query->where($column, '>=', Carbon::parse($request->input('start_date'))->startOfDay());
                }
                if ($request->filled('end_date')) {
                    $query->where($column, '<=', Carbon::parse($request->input('end_date'))->endOfDay());
                }
                break;
        }
    }

    private function getGroupByFormat(Request $request): string
    {
        $period = $request->input('period', 'month');
        if ($period === 'year' || $period === 'all') {
            return '%Y-%m'; // Group by month
        }
        return '%Y-%m-%d'; // Group by day
    }
}
