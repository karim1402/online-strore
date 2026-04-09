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
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportExport;

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

    public function itemsSummary(Request $request): JsonResponse
    {
        $query = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('stores', 'orders.store_id', '=', 'stores.id')
            ->where('orders.simple_status', 'delivered')
            ->whereNull('orders.deleted_at');

        $this->applyQueryFilter($query, $request, 'orders');

        if ($request->filled('module_id')) {
            $query->where('categories.module_id', $request->input('module_id'));
        }
        if ($request->filled('category_id')) {
            $query->where('categories.id', $request->input('category_id'));
        }
        if ($request->filled('store_id')) {
            $query->where('orders.store_id', $request->input('store_id'));
        }

        $items = $query->select(
            'products.id as product_id',
            'products.name_en as product_name',
            'categories.name_en as category_name',
            'modules.name_en as module_name',
            DB::raw('SUM(order_items.quantity) as quantity_sold'),
            DB::raw('MAX(order_items.unit_price) as unit_price'),
            DB::raw('SUM(order_items.total_price) as revenue')
        )
        ->join('modules', 'categories.module_id', '=', 'modules.id')
        ->groupBy(
            'products.id',
            'products.name_en',
            'categories.name_en',
            'modules.name_en'
        )
        ->orderBy('revenue', 'desc')
        ->get();

        $totalItemsSold = $items->sum('quantity_sold');
        $totalRevenue = $items->sum('revenue');

        return $this->successResponse([
            'total_items_sold' => (int) $totalItemsSold,
            'total_revenue'    => round((float) $totalRevenue, 2),
            'items'            => $items->map(function ($item) {
                return [
                    'product_id'    => $item->product_id,
                    'product_name'  => $item->product_name,
                    'category_name' => $item->category_name,
                    'module_name'   => $item->module_name,
                    'quantity_sold' => (int) $item->quantity_sold,
                    'unit_price'    => round((float) $item->unit_price, 2),
                    'revenue'       => round((float) $item->revenue, 2),
                ];
            })
        ], 'success.data_retrieved');
    }

    public function ordersByStore(Request $request): JsonResponse
    {
        $query = Order::where('simple_status', 'delivered')->with('store:id,name_en');
        $this->applyPeriodFilter($query, $request);

        if ($request->filled('module_id')) {
            $query->whereHas('items.product.category', function ($q) use ($request) {
                $q->where('module_id', $request->module_id);
            });
        }

        $rows = $query->select('store_id', DB::raw('COUNT(id) as orders_count'), DB::raw('SUM(total) as revenue'))
            ->groupBy('store_id')
            ->orderBy('orders_count', 'desc')
            ->get();

        $totalOrders = $rows->sum('orders_count');

        $data = $rows->map(function ($row) use ($totalOrders) {
            return [
                'store_id'     => $row->store_id,
                'store_name'   => collect($row->store)->get('name_en', 'Unknown'),
                'orders_count' => (int) $row->orders_count,
                'revenue'      => round((float) $row->revenue, 2),
                'percentage'   => $totalOrders > 0 ? round(((int) $row->orders_count / $totalOrders) * 100, 1) : 0,
            ];
        });

        return $this->successResponse($data, 'success.data_retrieved');
    }

    public function ordersByModule(Request $request): JsonResponse
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
            'modules.id as module_id',
            'modules.name_en as module_name',
            DB::raw('COUNT(DISTINCT orders.id) as orders_count'),
            DB::raw('SUM(order_items.total_price) as revenue')
        )
        ->groupBy('modules.id', 'modules.name_en')
        ->orderBy('orders_count', 'desc')
        ->get();

        $totalOrders = $rows->sum('orders_count');

        $data = $rows->map(function ($row) use ($totalOrders) {
            return [
                'module_id'    => $row->module_id,
                'module_name'  => $row->module_name,
                'orders_count' => (int) $row->orders_count,
                'revenue'      => round((float) $row->revenue, 2),
                'percentage'   => $totalOrders > 0 ? round(((int) $row->orders_count / $totalOrders) * 100, 1) : 0,
            ];
        });

        return $this->successResponse($data, 'success.data_retrieved');
    }

    public function ordersPerDay(Request $request): JsonResponse
    {
        $query = Order::where('simple_status', 'delivered');
        $this->applyPeriodFilter($query, $request);

        if ($request->filled('module_id')) {
            $query->whereHas('items.product.category', function ($q) use ($request) {
                $q->where('module_id', $request->module_id);
            });
        }

        $data = $query->select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(id) as count')
        )
        ->groupBy('date')
        ->orderBy('date', 'asc')
        ->get()
        ->map(function ($row) {
            return [
                'date'  => $row->date,
                'count' => (int) $row->count,
            ];
        });

        return $this->successResponse($data, 'success.data_retrieved');
    }

    public function deliveryVsPickup(Request $request): JsonResponse
    {
        $query = Order::where('simple_status', 'delivered');
        $this->applyPeriodFilter($query, $request);

        if ($request->filled('module_id')) {
            $query->whereHas('items.product.category', function ($q) use ($request) {
                $q->where('module_id', $request->module_id);
            });
        }

        $total = $query->count();
        $deliveredCount = (clone $query)->where('is_delivery', 1)->count();
        $pickupCount = $total - $deliveredCount; // The rest are taken away (0)

        return $this->successResponse([
            'delivered_count'      => $deliveredCount,
            'delivered_percentage' => $total > 0 ? round(($deliveredCount / $total) * 100, 1) : 0,
            'pickup_count'         => $pickupCount,
            'pickup_percentage'    => $total > 0 ? round(($pickupCount / $total) * 100, 1) : 0,
            'total'                => $total,
        ], 'success.data_retrieved');
    }

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

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('products.name_en', 'like', "%{$search}%")
                  ->orWhere('products.name_ar', 'like', "%{$search}%");
            });
        }

        $this->applyQueryFilter($query, $request, 'orders');

        $dataQuery = $query->select(
            'products.id',
            'products.name_en',
            DB::raw('SUM(order_items.quantity) as units_sold'),
            DB::raw('SUM(order_items.total_price) as revenue')
        )
        ->groupBy('products.id', 'products.name_en')
        ->orderBy($request->input('sort_by', 'units_sold'), 'desc');

        $limit = $request->input('limit', 50);
        if ($limit === 'all') {
            $data = $dataQuery->get();
        } else {
            $data = $dataQuery->limit((int) $limit)->get();
        }

        $productIds = $data->pluck('id');
        $products = Product::whereIn('id', $productIds)->with(['primaryImage', 'images'])->get()->keyBy('id');

        $data->transform(function($item) use ($products) {
            $item->image_url = $products[$item->id]->image_url ?? null;
            return $item;
        });

        return $this->successResponse($data, 'success.data_retrieved');
    }

    public function mostViewedProducts(Request $request): JsonResponse
    {
        $query = Product::orderBy('view_count', 'desc')
            ->select('id', 'name_en', 'view_count', 'is_active');

        $this->applyPeriodFilter($query, $request);

        $data = $query->limit($request->input('limit', 50))->get();

        return $this->successResponse($data, 'success.data_retrieved');
    }

    public function bestSellersFlagged(Request $request): JsonResponse
    {
        $query = Product::where('is_best_seller', true)
            ->with('category:id,name_en')
            ->select('id', 'name_en', 'category_id', 'base_price', 'offer_price');

        $this->applyPeriodFilter($query, $request);

        $data = $query->get();

        return $this->successResponse($data, 'success.data_retrieved');
    }

    public function productsWithOffers(Request $request): JsonResponse
    {
        $query = Product::whereNotNull('offer_price')
            ->where('is_active', true)
            ->select('id', 'name_en', 'base_price', 'offer_price');

        $this->applyPeriodFilter($query, $request);

        $data = $query->get()->map(function ($product) {
                $discount = $product->base_price > 0 
                    ? (($product->base_price - $product->offer_price) / $product->base_price) * 100 
                    : 0;
                $product->discount_percentage = round($discount, 2);
                return $product;
            });

        return $this->successResponse($data, 'success.data_retrieved');
    }


    public function productBuyers(Request $request, int $id): JsonResponse
    {
        $query = DB::table('users')
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->where('order_items.product_id', $id)
            ->where('orders.simple_status', 'delivered')
            ->whereNull('orders.deleted_at')
            ->whereNull('users.deleted_at');

        $this->applyQueryFilter($query, $request, 'orders');

        $data = $query->select(
            'users.id as user_id',
            'users.name as user_name',
            DB::raw('SUM(order_items.quantity) as quantity'),
            'orders.created_at as order_date'
        )
        ->groupBy('orders.id', 'users.id', 'users.name', 'orders.created_at')
        ->orderBy('orders.created_at', 'desc')
        ->get();

        return $this->successResponse($data, 'success.data_retrieved');
    }

    public function productOrders(Request $request, int $id): JsonResponse
    {
        $query = Order::query()
            ->whereHas('items', function ($q) use ($id) {
                $q->where('product_id', $id);
            })
            ->with([
                'user:id,name,email,phone',
                'items' => function ($q) use ($id) { $q->where('product_id', $id); }
            ])
            ->select([
                'orders.id',
                'orders.order_number',
                'orders.total',
                'orders.order_status',
                'orders.simple_status',
                'orders.payment_method',
                'orders.payment_status',
                'orders.is_delivery',
                'orders.delivery_fee',
                'orders.created_at',
                'orders.user_id',
                'orders.address_snapshot',
                'orders.notes',
            ]);

        $this->applyPeriodFilter($query, $request, 'orders.created_at');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        $limit = $request->input('limit', 15);
        $orders = $query->orderByDesc('created_at')->paginate($limit === 'all' ? 1000 : (int) $limit);

        $orders->getCollection()->transform(function ($order) {
            $item = $order->items->first();
            
            $formattedOrder = [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'quantity' => $item ? $item->quantity : 0,
                'unit_price' => $item ? $item->unit_price : '0.00',
                'item_total' => $item ? number_format($item->quantity * $item->unit_price, 2, '.', '') : '0.00',
                'order_total' => $order->total,
                'order_status' => $order->order_status,
                'simple_status' => $order->simple_status,
                'payment_method' => $order->payment_method,
                'payment_status' => $order->payment_status,
                'is_delivery' => (bool)$order->is_delivery,
                'delivery_fee' => $order->delivery_fee,
                'created_at' => $order->created_at,
                'customer' => $order->user ? [
                    'id' => $order->user->id,
                    'name' => $order->user->name,
                    'email' => $order->user->email,
                    'phone' => $order->user->phone,
                ] : null,
                'address_snapshot' => $order->address_snapshot,
                'notes' => $order->notes,
            ];
            
            return $formattedOrder;
        });

        return $this->successResponse($orders, 'Product orders retrieved successfully');
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

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%")
                  ->orWhere('users.phone', 'like', "%{$search}%");
            });
        }

        $this->applyQueryFilter($query, $request, 'orders');

        $dataQuery = $query->select(
            'users.id',
            'users.name',
            'users.email',
            'users.phone',
            DB::raw('COUNT(orders.id) as order_count'),
            DB::raw('SUM(orders.total) as total_spend')
        )
        ->groupBy('users.id', 'users.name', 'users.email', 'users.phone')
        ->orderBy('total_spend', 'desc');

        $limit = $request->input('limit', 50);

        if ($limit === 'all') {
            $data = $dataQuery->paginate($request->input('per_page', 15));
        } else {
            $data = $dataQuery->limit((int)$limit)->get();
        }

        return $this->successResponse($data, 'success.data_retrieved');
    }

    public function inactiveUsers(Request $request): JsonResponse
    {
        $days = $request->input('days_inactive', 30);
        $cutoffDate = Carbon::now()->subDays($days);

        // Users who registered but never placed an order OR haven't placed an order recently
        $query = User::whereDoesntHave('orders', function ($q) use ($cutoffDate) {
                $q->where('created_at', '>=', $cutoffDate);
            })
            ->select('id', 'name', 'email', 'created_at');

        // Filter users by registration date range
        $this->applyPeriodFilter($query, $request);

        $data = $query->paginate($request->input('per_page', 50));

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
            'deliveries.id as driver_id',
            'deliveries.name as driver_name',
            DB::raw('SUM(CASE WHEN orders.simple_status = "delivered" THEN 1 ELSE 0 END) as deliveries_completed')
        )
        ->groupBy('deliveries.id', 'deliveries.name')
        ->orderBy('deliveries_completed', 'desc')
        ->get()
        ->map(function ($row) {
            return [
                'driver_id'            => $row->driver_id,
                'driver_name'          => $row->driver_name,
                'deliveries_completed' => (int) $row->deliveries_completed,
                'average_rating'       => "0.0" // Ratings not yet implemented
            ];
        });

        return $this->successResponse($data, 'success.data_retrieved');
    }

    public function driverAvailability(Request $request): JsonResponse
    {
        $query = Delivery::select('status', 'availability', DB::raw('COUNT(*) as count'));

        $this->applyPeriodFilter($query, $request);

        $data = $query->groupBy('status', 'availability')->get();

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
        $period   = $request->input('period', 'all');
        $now      = Carbon::now();

        $query = Voucher::withCount(['usages' => function ($q) use ($request, $period, $now) {
                if ($period === 'today') {
                    $q->whereDate('created_at', $now->toDateString());
                } elseif ($period === 'week') {
                    $q->where('created_at', '>=', $now->copy()->subWeek()->startOfDay());
                } elseif ($period === 'month') {
                    $q->where('created_at', '>=', $now->copy()->subMonth()->startOfDay());
                } elseif ($period === 'year') {
                    $q->where('created_at', '>=', $now->copy()->subYear()->startOfDay());
                } elseif ($period === 'custom') {
                    if ($request->filled('start_date')) {
                        $q->where('created_at', '>=', Carbon::parse($request->input('start_date'))->startOfDay());
                    }
                    if ($request->filled('end_date')) {
                        $q->where('created_at', '<=', Carbon::parse($request->input('end_date'))->endOfDay());
                    }
                }
            }])
            ->withSum(['usages' => function ($q) use ($request, $period, $now) {
                if ($period === 'today') {
                    $q->whereDate('created_at', $now->toDateString());
                } elseif ($period === 'week') {
                    $q->where('created_at', '>=', $now->copy()->subWeek()->startOfDay());
                } elseif ($period === 'month') {
                    $q->where('created_at', '>=', $now->copy()->subMonth()->startOfDay());
                } elseif ($period === 'year') {
                    $q->where('created_at', '>=', $now->copy()->subYear()->startOfDay());
                } elseif ($period === 'custom') {
                    if ($request->filled('start_date')) {
                        $q->where('created_at', '>=', Carbon::parse($request->input('start_date'))->startOfDay());
                    }
                    if ($request->filled('end_date')) {
                        $q->where('created_at', '<=', Carbon::parse($request->input('end_date'))->endOfDay());
                    }
                }
            }], 'discount_amount')
            ->orderBy('usages_count', 'desc');

        if ($request->has('status')) {
            $query->where('is_active', filter_var($request->status, FILTER_VALIDATE_BOOLEAN));
        }

        $vouchers = $query->get()->map(function($voucher) {
            return [
                'id'                    => $voucher->id,
                'code'                  => $voucher->code,
                'type'                  => $voucher->type,
                'value'                 => $voucher->value,
                'is_active'             => $voucher->is_active,
                'usage_count'           => $voucher->usages_count,
                'total_discount_given'  => (float) $voucher->usages_sum_discount_amount,
                'limit'                 => $voucher->usage_limit,
                'utilization_percentage' => $voucher->usage_limit
                    ? round(($voucher->usages_count / $voucher->usage_limit) * 100, 2)
                    : null,
            ];
        });

        return $this->successResponse($vouchers, 'success.data_retrieved');
    }

    public function voucherUsers(Request $request, int $voucher_id): JsonResponse
    {
        $voucher = Voucher::find($voucher_id);
        if (!$voucher) {
            return response()->json(['success' => false, 'message' => 'Voucher not found'], 404);
        }

        $query = DB::table('voucher_usages')
            ->join('users', 'voucher_usages.user_id', '=', 'users.id')
            ->where('voucher_usages.voucher_id', $voucher_id)
            ->whereNull('users.deleted_at');

        // Search by name, email, or phone
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%")
                  ->orWhere('users.phone', 'like', "%{$search}%");
            });
        }

        // Date filters on voucher_usages.created_at
        $period = $request->input('period', 'all');
        $now = Carbon::now();
        switch ($period) {
            case 'today':
                $query->whereDate('voucher_usages.created_at', $now->toDateString());
                break;
            case 'week':
                $query->where('voucher_usages.created_at', '>=', $now->copy()->subWeek()->startOfDay());
                break;
            case 'month':
                $query->where('voucher_usages.created_at', '>=', $now->copy()->subMonth()->startOfDay());
                break;
            case 'year':
                $query->where('voucher_usages.created_at', '>=', $now->copy()->subYear()->startOfDay());
                break;
            case 'custom':
                if ($request->filled('start_date')) {
                    $query->where('voucher_usages.created_at', '>=', Carbon::parse($request->input('start_date'))->startOfDay());
                }
                if ($request->filled('end_date')) {
                    $query->where('voucher_usages.created_at', '<=', Carbon::parse($request->input('end_date'))->endOfDay());
                }
                break;
        }

        $dataQuery = $query->select(
            'users.id as user_id',
            'users.name as user_name',
            'users.email',
            'users.phone',
            DB::raw('COUNT(voucher_usages.id) as usage_count'),
            DB::raw('SUM(voucher_usages.discount_amount) as total_discount_received'),
            DB::raw('MAX(voucher_usages.created_at) as last_used_at')
        )
        ->groupBy('users.id', 'users.name', 'users.email', 'users.phone')
        ->orderBy('last_used_at', 'desc');

        $perPage = $request->input('per_page', $request->input('limit', 15));
        $data = $dataQuery->paginate((int) $perPage);

        return $this->successResponse($data, 'Voucher users retrieved successfully');
    }

    public function voucherUserOrders(Request $request, int $voucher_id, int $user_id): JsonResponse
    {
        $voucher = Voucher::find($voucher_id);
        if (!$voucher) {
            return response()->json(['success' => false, 'message' => 'Voucher not found'], 404);
        }

        $query = DB::table('voucher_usages')
            ->join('orders', 'voucher_usages.order_id', '=', 'orders.id')
            ->where('voucher_usages.voucher_id', $voucher_id)
            ->where('voucher_usages.user_id', $user_id)
            ->whereNull('orders.deleted_at')
            ->select(
                'orders.id as order_id',
                'orders.order_number',
                'orders.created_at',
                'orders.simple_status as status',
                'orders.total as order_total',
                'voucher_usages.discount_amount as discount_applied',
                'orders.payment_method'
            )
            ->orderBy('orders.created_at', 'desc');

        $perPage = $request->input('per_page', $request->input('limit', 15));
        $data = $query->paginate((int) $perPage);

        // Format order_number display
        $data->getCollection()->transform(function ($row) {
            $row->order_number = '#MK-' . $row->order_id;
            return $row;
        });

        return $this->successResponse($data, 'User voucher orders retrieved successfully');
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

    public function storeStatusOverview(Request $request): JsonResponse
    {
        $query = Store::select('status', DB::raw('COUNT(id) as count'));

        $this->applyPeriodFilter($query, $request);

        $data = $query->groupBy('status')->get();

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

    // ──────────────────────────────────────────
    // 9. EVENTUAL EXPORT FOR ALL REPORTS
    // ──────────────────────────────────────────

    public function export(Request $request)
    {
        $reportType = $request->input('report_type');
        $headings = [];
        $mapper = null;
        $data = [];

        // Force a large limit for paginated queries during export
        $request->merge(['per_page' => 100000]);

        switch ($reportType) {
            case 'revenue_by_module':
                $data = $this->revenueByModule($request)->getData(true)['data'] ?? [];
                $headings = ['Module', 'Orders', 'Revenue', 'Percentage %'];
                $mapper = fn($row) => [$row['label'], $row['orders'], $row['revenue'], $row['percentage']];
                break;
            case 'revenue_by_category':
                $data = $this->revenueByCategory($request)->getData(true)['data'] ?? [];
                $headings = ['Category', 'Orders', 'Revenue'];
                $mapper = fn($row) => [$row['category'], $row['order_count'], $row['revenue']];
                break;
            case 'revenue_by_store':
                $data = $this->revenueByStore($request)->getData(true)['data'] ?? [];
                $headings = ['Store ID', 'Store Name', 'Orders', 'Revenue'];
                $mapper = fn($row) => [$row['store_id'], $row['store_name'], $row['order_count'], $row['revenue']];
                break;
            case 'revenue_by_payment_method':
                $data = $this->revenueByPaymentMethod($request)->getData(true)['data'] ?? [];
                $headings = ['Payment Method', 'Orders', 'Revenue'];
                $mapper = fn($row) => [$row['payment_method'], $row['order_count'], $row['revenue']];
                break;
            case 'revenue_trend':
                $data = $this->revenueTrend($request)->getData(true)['data'] ?? [];
                $headings = ['Date', 'Gross Revenue', 'Net Revenue'];
                $mapper = fn($row) => [$row['date'], $row['gross_revenue'], $row['net_revenue']];
                break;
            case 'payment_methods':
                $data = $this->paymentMethodsBreakdown($request)->getData(true)['data'] ?? [];
                $headings = ['Method', 'Label', 'Count', 'Percentage %'];
                $mapper = fn($row) => [$row['method'], $row['label'], $row['count'], $row['percentage']];
                break;
            case 'order_distribution':
                $data = $this->orderDistribution($request)->getData(true)['data']['statuses'] ?? [];
                $headings = ['Status', 'Label', 'Count', 'Percentage %'];
                $mapper = fn($row) => [$row['status'], $row['label'], $row['count'], $row['percentage']];
                break;
            case 'deliveries_per_day':
                $data = $this->deliveriesPerDay($request)->getData(true)['data'] ?? [];
                $headings = ['Date', 'Day', 'Deliveries'];
                $mapper = fn($row) => [$row['date'], $row['day_label'], $row['deliveries']];
                break;
            case 'monthly_payment_trend':
                $data = $this->monthlyPaymentTrend($request)->getData(true)['data'] ?? [];
                $headings = ['Month', 'Label', 'Successful', 'Failed', 'Total'];
                $mapper = fn($row) => [$row['month'], $row['month_label'], $row['successful'], $row['failed'], $row['total']];
                break;
            case 'top_selling_products':
                $data = $this->topSellingProducts($request)->getData(true)['data'] ?? [];
                $headings = ['Product ID', 'Product Name', 'Units Sold', 'Revenue'];
                $mapper = fn($row) => [$row['id'], $row['name_en'], $row['units_sold'], $row['revenue']];
                break;
            case 'most_viewed_products':
                $data = $this->mostViewedProducts()->getData(true)['data'] ?? [];
                $headings = ['Product ID', 'Product Name', 'Views', 'Active'];
                $mapper = fn($row) => [$row['id'], $row['name_en'], $row['view_count'], $row['is_active'] ? 'Yes' : 'No'];
                break;
            case 'best_sellers':
                $data = $this->bestSellersFlagged()->getData(true)['data'] ?? [];
                $headings = ['Product ID', 'Product Name', 'Category', 'Base Price', 'Offer Price'];
                $mapper = fn($row) => [$row['id'], $row['name_en'], $row['category']['name_en'] ?? '', $row['base_price'], $row['offer_price']];
                break;
            case 'products_with_offers':
                $data = $this->productsWithOffers()->getData(true)['data'] ?? [];
                $headings = ['Product ID', 'Product Name', 'Base Price', 'Offer Price', 'Discount %'];
                $mapper = fn($row) => [$row['id'], $row['name_en'], $row['base_price'], $row['offer_price'], $row['discount_percentage']];
                break;
            case 'top_customers':
                $data = $this->topCustomers($request)->getData(true)['data'] ?? [];
                $headings = ['Customer ID', 'Name', 'Email', 'Phone', 'Orders', 'Total Spend'];
                $mapper = fn($row) => [$row['id'], $row['name'], $row['email'], $row['phone'], $row['order_count'], $row['total_spend']];
                break;
            case 'inactive_users':
                $paginated = $this->inactiveUsers($request)->getData(true)['data'] ?? [];
                $data = $paginated['data'] ?? [];
                $headings = ['User ID', 'Name', 'Email', 'Registered At'];
                $mapper = fn($row) => [$row['id'], $row['name'], $row['email'], Carbon::parse($row['created_at'])->format('Y-m-d')];
                break;
            case 'driver_performance':
                $data = $this->driverPerformance($request)->getData(true)['data'] ?? [];
                $headings = ['Driver ID', 'Name', 'Vehicle Type', 'Successful Deliveries', 'Failed Deliveries'];
                $mapper = fn($row) => [$row['id'], $row['name'], $row['vehicle_type'], $row['successful_deliveries'], $row['failed_deliveries']];
                break;
            case 'driver_availability':
                $data = $this->driverAvailability()->getData(true)['data'] ?? [];
                $headings = ['Status', 'Availability', 'Count'];
                $mapper = fn($row) => [$row['status'], $row['availability'], $row['count']];
                break;
            case 'voucher_usage':
                $data = $this->voucherUsageAndEffectiveness($request)->getData(true)['data'] ?? [];
                $headings = ['Voucher ID', 'Code', 'Type', 'Value', 'Active', 'Usages', 'Total Discount Given', 'Usage Limit', 'Utilization %'];
                $mapper = fn($row) => [$row['id'], $row['code'], $row['type'], $row['value'], $row['is_active'] ? 'Yes' : 'No', $row['usage_count'], $row['total_discount_given'], $row['limit'] ?? 'Unlimited', $row['utilization_percentage']];
                break;
            case 'voucher_users':
                $voucherId = (int) $request->input('voucher_id');
                $paginated = $this->voucherUsers($request, $voucherId)->getData(true)['data'] ?? [];
                $data = $paginated['data'] ?? [];
                $headings = ['User Name', 'Email', 'Phone', 'Usage Count', 'Total Discount Received', 'Last Used Date'];
                $mapper = fn($row) => [
                    $row['user_name'],
                    $row['email'],
                    $row['phone'],
                    $row['usage_count'],
                    $row['total_discount_received'],
                    Carbon::parse($row['last_used_at'])->format('Y-m-d H:i:s'),
                ];
                break;
            case 'store_status_overview':
                $data = $this->storeStatusOverview()->getData(true)['data'] ?? [];
                $headings = ['Status', 'Count'];
                $mapper = fn($row) => [$row['status'], $row['count']];
                break;
            case 'order_items_summary':
                $summaryData = $this->itemsSummary($request)->getData(true)['data'] ?? [];
                $data = $summaryData['items'] ?? [];
                $headings = ['Product ID', 'Product Name', 'Category', 'Module', 'Quantity Sold', 'Unit Price', 'Revenue'];
                $mapper = fn($row) => [$row['product_id'], $row['product_name'], $row['category_name'], $row['module_name'], $row['quantity_sold'], $row['unit_price'], $row['revenue']];
                break;
            case 'orders_by_store':
                $data = $this->ordersByStore($request)->getData(true)['data'] ?? [];
                $headings = ['Store ID', 'Store Name', 'Orders', 'Revenue', 'Percentage %'];
                $mapper = fn($row) => [$row['store_id'], $row['store_name'], $row['orders_count'], $row['revenue'], $row['percentage']];
                break;
            case 'orders_by_module':
                $data = $this->ordersByModule($request)->getData(true)['data'] ?? [];
                $headings = ['Module ID', 'Module Name', 'Orders', 'Revenue', 'Percentage %'];
                $mapper = fn($row) => [$row['module_id'], $row['module_name'], $row['orders_count'], $row['revenue'], $row['percentage']];
                break;
            case 'orders_per_day':
                $data = $this->ordersPerDay($request)->getData(true)['data'] ?? [];
                $headings = ['Date', 'Count'];
                $mapper = fn($row) => [$row['date'], $row['count']];
                break;
            case 'delivery_vs_pickup':
                $metrics = $this->deliveryVsPickup($request)->getData(true)['data'] ?? [];
                $data = [
                    [
                        'Delivery',
                        $metrics['delivered_count'] ?? 0,
                        $metrics['delivered_percentage'] ?? 0
                    ],
                    [
                        'Pickup',
                        $metrics['pickup_count'] ?? 0,
                        $metrics['pickup_percentage'] ?? 0
                    ]
                ];
                $headings = ['Order Type', 'Count', 'Percentage %'];
                $mapper = fn($row) => $row;
                break;
            default:
                return response()->json(['success' => false, 'message' => 'Invalid report type for export'], 400);
        }

        return Excel::download(new ReportExport(collect($data), $headings, $mapper), "{$reportType}_export_" . now()->format('YmdHis') . ".xlsx");
    }
}
