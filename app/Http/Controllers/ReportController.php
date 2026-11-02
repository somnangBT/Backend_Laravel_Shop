<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the main reports page (defaults to Customer Report)
     */
    public function index(Request $request)
    {
        return $this->customerReport($request);
    }

    /**
     * Customer Report view (Daily, Monthly, Yearly)
     */
    public function customerReport(Request $request)
    {
        $data = $this->fetchCustomerReportData($request);
        return view('reports.customers', $data);
    }

    /**
     * Export Customer Report to Excel (.xls or .csv)
     */
    public function exportCustomerReport(Request $request)
    {
        $data = $this->fetchCustomerReportData($request);
        $format = strtolower($request->get('format', 'excel'));

        if ($format === 'csv') {
            return $this->exportToCsv($data);
        }

        return $this->exportToExcel($data);
    }

    /**
     * Sales & Revenue Report View (Daily, Monthly, Yearly)
     */
    public function salesReport(Request $request)
    {
        $data = $this->fetchSalesReportData($request, false);
        return view('reports.sales', $data);
    }

    /**
     * Export Sales Report to Excel (.xls or .csv)
     */
    public function exportSalesReport(Request $request)
    {
        $data = $this->fetchSalesReportData($request, true);
        $format = strtolower($request->get('format', 'excel'));

        if ($format === 'csv') {
            return $this->exportSalesToCsv($data);
        }

        return $this->exportSalesToExcel($data);
    }

    /**
     * Helper to compute sales report metrics, top products, payment breakdown & order list
     */
    private function fetchSalesReportData(Request $request, bool $forExport = false): array
    {
        $period = $request->get('period', 'daily');
        $now = Carbon::now();

        switch ($period) {
            case 'monthly':
                $monthStr = $request->get('month', $now->format('Y-m'));
                $startDate = Carbon::parse($monthStr . '-01')->startOfMonth();
                $endDate = Carbon::parse($monthStr . '-01')->endOfMonth();
                $periodLabel = 'Monthly (' . $startDate->format('F Y') . ')';
                $rangeText = $startDate->format('M d, Y') . ' — ' . $endDate->format('M d, Y');
                break;

            case 'yearly':
                $yearStr = $request->get('year', $now->format('Y'));
                $startDate = Carbon::createFromDate((int)$yearStr, 1, 1)->startOfYear();
                $endDate = Carbon::createFromDate((int)$yearStr, 12, 31)->endOfYear();
                $periodLabel = 'Yearly (' . $yearStr . ')';
                $rangeText = 'Jan 01, ' . $yearStr . ' — Dec 31, ' . $yearStr;
                break;

            case 'custom':
                $startDateInput = $request->get('start_date', $now->copy()->subDays(7)->format('Y-m-d'));
                $endDateInput = $request->get('end_date', $now->format('Y-m-d'));
                $startDate = Carbon::parse($startDateInput)->startOfDay();
                $endDate = Carbon::parse($endDateInput)->endOfDay();
                if ($startDate->gt($endDate)) {
                    $tmp = $startDate;
                    $startDate = $endDate->copy()->startOfDay();
                    $endDate = $tmp->copy()->endOfDay();
                }
                $periodLabel = 'Custom Range';
                $rangeText = $startDate->format('M d, Y') . ' — ' . $endDate->format('M d, Y');
                break;

            case 'daily':
            default:
                $period = 'daily';
                $dateStr = $request->get('date', $now->format('Y-m-d'));
                $startDate = Carbon::parse($dateStr)->startOfDay();
                $endDate = Carbon::parse($dateStr)->endOfDay();
                $periodLabel = 'Daily (' . $startDate->format('M d, Y') . ')';
                $rangeText = $startDate->format('l, F d, Y');
                break;
        }

        // Base query for orders in this period
        $periodOrdersQuery = Order::whereBetween('created_at', [$startDate, $endDate]);

        // Status filter
        $statusFilter = $request->get('status', 'all');
        if ($statusFilter !== 'all' && in_array($statusFilter, ['pending', 'preparing', 'delivering', 'completed', 'cancelled'])) {
            $periodOrdersQuery->where('status', $statusFilter);
        }

        // Payment method filter
        $paymentFilter = $request->get('payment_method', 'all');
        if ($paymentFilter !== 'all') {
            $periodOrdersQuery->where('payment_method', $paymentFilter);
        }

        // KPI metrics
        $allPeriodOrders = (clone $periodOrdersQuery)->get();
        $totalOrdersCount = $allPeriodOrders->count();
        $validOrders = $allPeriodOrders->where('status', '!=', 'cancelled');
        $totalSales = $validOrders->sum('total');
        $paidSales = $allPeriodOrders->where('payment_status', 'paid')->sum('total');
        $completedCount = $allPeriodOrders->where('status', 'completed')->count();
        $pendingCount = $allPeriodOrders->whereIn('status', ['pending', 'preparing', 'delivering'])->count();
        $cancelledCount = $allPeriodOrders->where('status', 'cancelled')->count();
        $avgOrderValue = $validOrders->count() > 0 ? ($totalSales / $validOrders->count()) : 0;

        // Payment method breakdown
        $paymentBreakdown = $allPeriodOrders->groupBy('payment_method')->map(function ($orders, $method) {
            return [
                'method' => $method ?: 'Cash',
                'count' => $orders->count(),
                'total' => $orders->where('status', '!=', 'cancelled')->sum('total')
            ];
        });

        // Top 5 Best Selling Products in this period
        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.pro_id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', '!=', 'cancelled')
            ->select(
                'products.pro_id',
                'products.pro_name as name',
                'products.price',
                'products.image',
                DB::raw('SUM(order_items.quantity) as total_qty'),
                DB::raw('SUM(order_items.quantity * order_items.price) as total_revenue')
            )
            ->groupBy('products.pro_id', 'products.pro_name', 'products.price', 'products.image')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // Orders list for table / export
        $ordersQuery = (clone $periodOrdersQuery)->with(['user', 'orderItems.product'])->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $ordersQuery->where(function ($q) use ($search) {
                $q->where('id', $search)
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $forExport ? $ordersQuery->get() : $ordersQuery->paginate(15)->withQueryString();

        $summary = [
            'period' => $period,
            'period_label' => $periodLabel,
            'range_text' => $rangeText,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'generated_at' => Carbon::now(),
            'total_sales' => $totalSales,
            'paid_sales' => $paidSales,
            'total_orders' => $totalOrdersCount,
            'completed_orders' => $completedCount,
            'pending_orders' => $pendingCount,
            'cancelled_orders' => $cancelledCount,
            'avg_order_value' => $avgOrderValue,
            'selected_date' => $request->get('date', $now->format('Y-m-d')),
            'selected_month' => $request->get('month', $now->format('Y-m')),
            'selected_year' => $request->get('year', $now->format('Y')),
            'selected_status' => $statusFilter,
            'selected_payment' => $paymentFilter,
            'start_date_input' => $request->get('start_date', $now->copy()->subDays(7)->format('Y-m-d')),
            'end_date_input' => $request->get('end_date', $now->format('Y-m-d')),
            'search' => $request->get('search', ''),
        ];

        return compact('summary', 'orders', 'topProducts', 'paymentBreakdown');
    }

    /**
     * Export Sales to Excel (.xls)
     */
    private function exportSalesToExcel(array $data)
    {
        $filename = 'Sales_Report_' . ucfirst($data['summary']['period']) . '_' . Carbon::now()->format('Ymd_His') . '.xls';

        $html = view('reports.export_sales_excel', $data)->render();

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        return response($html, 200, $headers);
    }

    /**
     * Export Sales to CSV
     */
    private function exportSalesToCsv(array $data)
    {
        $orders = $data['orders'];
        $summary = $data['summary'];

        $filename = 'Sales_Report_' . ucfirst($summary['period']) . '_' . Carbon::now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($orders, $summary) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM
            fputs($handle, "\xEF\xBB\xBF");

            // Header
            fputcsv($handle, ['DELICIOUSEATS - SALES & REVENUE REPORT']);
            fputcsv($handle, ['Period:', $summary['period_label']]);
            fputcsv($handle, ['Date Range:', $summary['start_date']->format('Y-m-d') . ' to ' . $summary['end_date']->format('Y-m-d')]);
            fputcsv($handle, ['Generated At:', $summary['generated_at']->format('Y-m-d H:i:s')]);
            fputcsv($handle, []);

            // Summary KPIs
            fputcsv($handle, ['SUMMARY METRICS']);
            fputcsv($handle, ['Total Sales ($)', 'Paid Sales ($)', 'Total Orders', 'Completed Orders', 'Pending Orders', 'Cancelled Orders', 'Average Order Value ($)']);
            fputcsv($handle, [
                number_format($summary['total_sales'], 2),
                number_format($summary['paid_sales'], 2),
                $summary['total_orders'],
                $summary['completed_orders'],
                $summary['pending_orders'],
                $summary['cancelled_orders'],
                number_format($summary['avg_order_value'], 2),
            ]);
            fputcsv($handle, []);

            // Data Table Headers
            fputcsv($handle, [
                '#',
                'Order ID',
                'Date & Time',
                'Customer Name',
                'Email',
                'Phone',
                'Items Count',
                'Subtotal ($)',
                'Discount ($)',
                'Tax ($)',
                'Shipping ($)',
                'Total ($)',
                'Payment Method',
                'Payment Status',
                'Order Status',
            ]);

            $index = 1;
            foreach ($orders as $order) {
                $customerName = optional($order->user)->name ?? ($order->email ?? 'Guest');
                fputcsv($handle, [
                    $index++,
                    '#' . $order->id,
                    $order->created_at ? $order->created_at->format('Y-m-d H:i') : 'N/A',
                    $customerName,
                    $order->email ?? optional($order->user)->email ?? 'N/A',
                    $order->phone ?? 'N/A',
                    $order->orderItems ? $order->orderItems->sum('quantity') : 0,
                    number_format($order->subtotal, 2),
                    number_format($order->discount, 2),
                    number_format($order->tax, 2),
                    number_format($order->shipping, 2),
                    number_format($order->total, 2),
                    strtoupper($order->payment_method ?? 'cash'),
                    ucfirst($order->payment_status ?? 'pending'),
                    ucfirst($order->status ?? 'pending'),
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, [
                'TOTAL',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                number_format($summary['total_sales'], 2),
                '',
                '',
                '',
            ]);

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Helper to compute customer report metrics & list
     */
    private function fetchCustomerReportData(Request $request): array
    {
        $period = $request->get('period', 'daily'); // 'daily', 'monthly', 'yearly', 'custom'
        $now = Carbon::now();

        switch ($period) {
            case 'monthly':
                $monthStr = $request->get('month', $now->format('Y-m'));
                $startDate = Carbon::parse($monthStr . '-01')->startOfMonth();
                $endDate = Carbon::parse($monthStr . '-01')->endOfMonth();
                $periodLabel = 'Monthly (' . $startDate->format('F Y') . ')';
                $selectedDate = $now->toDateString();
                $selectedMonth = $monthStr;
                $selectedYear = (int)$startDate->year;
                break;

            case 'yearly':
                $year = (int)$request->get('year', $now->year);
                $startDate = Carbon::createFromDate($year, 1, 1)->startOfYear();
                $endDate = Carbon::createFromDate($year, 12, 31)->endOfYear();
                $periodLabel = 'Yearly (' . $year . ')';
                $selectedDate = $now->toDateString();
                $selectedMonth = $now->format('Y-m');
                $selectedYear = $year;
                break;

            case 'custom':
                $startDateStr = $request->get('start_date', $now->copy()->subDays(30)->toDateString());
                $endDateStr = $request->get('end_date', $now->toDateString());
                $startDate = Carbon::parse($startDateStr)->startOfDay();
                $endDate = Carbon::parse($endDateStr)->endOfDay();
                $periodLabel = 'Custom (' . $startDate->format('M d, Y') . ' - ' . $endDate->format('M d, Y') . ')';
                $selectedDate = $now->toDateString();
                $selectedMonth = $now->format('Y-m');
                $selectedYear = (int)$now->year;
                break;

            case 'daily':
            default:
                $period = 'daily';
                $dateStr = $request->get('date', $now->toDateString());
                $startDate = Carbon::parse($dateStr)->startOfDay();
                $endDate = Carbon::parse($dateStr)->endOfDay();
                $periodLabel = 'Daily (' . $startDate->format('M d, Y') . ')';
                $selectedDate = $startDate->toDateString();
                $selectedMonth = $now->format('Y-m');
                $selectedYear = (int)$now->year;
                break;
        }

        $search = trim($request->get('search', ''));
        $statusFilter = $request->get('status', 'all'); // 'all', 'active', 'new', 'inactive'
        $sourceFilter = $request->get('source', 'all'); // 'all', 'clients', 'users'

        // 1. Gather all unique customers
        $customersList = collect();

        // From Clients table
        if ($sourceFilter === 'all' || $sourceFilter === 'clients') {
            $clientsQuery = Client::query();
            if ($search !== '') {
                $clientsQuery->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('address', 'like', "%{$search}%");
                });
            }
            $clients = $clientsQuery->get();
            foreach ($clients as $client) {
                $customersList->push((object)[
                    'id' => 'C-' . $client->id,
                    'raw_id' => $client->id,
                    'name' => $client->name,
                    'email' => $client->email,
                    'phone' => $client->phone ?: 'N/A',
                    'address' => $client->address ?: 'N/A',
                    'created_at' => $client->created_at ? Carbon::parse($client->created_at) : null,
                    'source' => 'Client',
                ]);
            }
        }

        // From Users table (role = 'user')
        if ($sourceFilter === 'all' || $sourceFilter === 'users') {
            $usersQuery = User::where('role', 'user');
            if ($search !== '') {
                $usersQuery->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }
            $users = $usersQuery->get();
            foreach ($users as $user) {
                // Avoid duplicate if email already exists in Clients
                if ($customersList->contains('email', $user->email)) {
                    continue;
                }
                $customersList->push((object)[
                    'id' => 'U-' . $user->id,
                    'raw_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => 'N/A',
                    'address' => 'N/A',
                    'created_at' => $user->created_at ? Carbon::parse($user->created_at) : null,
                    'source' => 'User Account',
                ]);
            }
        }

        // 2. Query Orders to compute statistics
        $allOrders = Order::all();

        $processedCustomers = $customersList->map(function ($customer) use ($allOrders, $startDate, $endDate) {
            // Find orders by user_id or email or phone
            $userOrders = $allOrders->filter(function ($order) use ($customer) {
                if ($customer->source === 'User Account' && $order->user_id == $customer->raw_id) {
                    return true;
                }
                if (!empty($customer->email) && strtolower($order->email ?? '') === strtolower($customer->email)) {
                    return true;
                }
                if (!empty($customer->phone) && $customer->phone !== 'N/A' && ($order->phone ?? '') === $customer->phone) {
                    return true;
                }
                return false;
            });

            // Update phone and address from latest order if N/A
            $latestOrder = $userOrders->sortByDesc('created_at')->first();
            if ($latestOrder) {
                if ($customer->phone === 'N/A' && !empty($latestOrder->phone)) {
                    $customer->phone = $latestOrder->phone;
                }
                if ($customer->address === 'N/A' && !empty($latestOrder->shipping_address)) {
                    $customer->address = $latestOrder->shipping_address;
                }
            }

            // Orders within filtered period
            $periodOrders = $userOrders->filter(function ($order) use ($startDate, $endDate) {
                if (!$order->created_at) return false;
                $orderDate = Carbon::parse($order->created_at);
                return $orderDate->between($startDate, $endDate);
            });

            $customer->period_orders_count = $periodOrders->count();
            $customer->period_total_spent = (float)$periodOrders->sum('total');

            $customer->total_orders = $userOrders->count();
            $customer->total_spent = (float)$userOrders->sum('total');
            $customer->last_order_date = $latestOrder && $latestOrder->created_at ? Carbon::parse($latestOrder->created_at) : null;

            // Status determination
            $isRegisteredInPeriod = $customer->created_at && $customer->created_at->between($startDate, $endDate);
            if ($customer->period_orders_count > 0) {
                $customer->status = 'Active';
            } elseif ($isRegisteredInPeriod) {
                $customer->status = 'New';
            } elseif ($customer->total_orders > 0) {
                $customer->status = 'Regular';
            } else {
                $customer->status = 'Inactive';
            }

            $customer->is_new_in_period = $isRegisteredInPeriod;

            return $customer;
        });

        // 3. Filter by status if requested
        if ($statusFilter === 'active') {
            $processedCustomers = $processedCustomers->filter(fn($c) => $c->status === 'Active');
        } elseif ($statusFilter === 'new') {
            $processedCustomers = $processedCustomers->filter(fn($c) => $c->status === 'New');
        } elseif ($statusFilter === 'inactive') {
            $processedCustomers = $processedCustomers->filter(fn($c) => in_array($c->status, ['Inactive', 'Regular']));
        }

        // Summary calculations
        $summary = [
            'period' => $period,
            'period_label' => $periodLabel,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'selected_date' => $selectedDate,
            'selected_month' => $selectedMonth,
            'selected_year' => $selectedYear,
            'total_customers' => $processedCustomers->count(),
            'new_customers_count' => $processedCustomers->where('is_new_in_period', true)->count(),
            'active_customers_count' => $processedCustomers->where('period_orders_count', '>', 0)->count(),
            'period_orders_count' => $processedCustomers->sum('period_orders_count'),
            'period_total_revenue' => $processedCustomers->sum('period_total_spent'),
            'all_time_revenue' => $processedCustomers->sum('total_spent'),
            'generated_at' => Carbon::now(),
            'search' => $search,
            'status' => $statusFilter,
            'source' => $sourceFilter,
        ];

        return [
            'customers' => $processedCustomers,
            'summary' => $summary,
        ];
    }

    /**
     * Export to Formatted Microsoft Excel (.xls)
     */
    private function exportToExcel(array $data)
    {
        $customers = $data['customers'];
        $summary = $data['summary'];

        $filename = 'Customer_Report_' . ucfirst($summary['period']) . '_' . Carbon::now()->format('Ymd_His') . '.xls';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        $html = view('reports.export_excel', compact('customers', 'summary'))->render();

        return response($html, 200, $headers);
    }

    /**
     * Export to CSV with UTF-8 BOM
     */
    private function exportToCsv(array $data)
    {
        $customers = $data['customers'];
        $summary = $data['summary'];

        $filename = 'Customer_Report_' . ucfirst($summary['period']) . '_' . Carbon::now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($customers, $summary) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Microsoft Excel compatibility
            fputs($handle, "\xEF\xBB\xBF");

            // Report Header
            fputcsv($handle, ['DELICIOUSEATS - CUSTOMER DATA REPORT']);
            fputcsv($handle, ['Period:', $summary['period_label']]);
            fputcsv($handle, ['Date Range:', $summary['start_date']->format('Y-m-d') . ' to ' . $summary['end_date']->format('Y-m-d')]);
            fputcsv($handle, ['Generated At:', $summary['generated_at']->format('Y-m-d H:i:s')]);
            fputcsv($handle, []);

            // Summary KPIs
            fputcsv($handle, ['SUMMARY STATISTICS']);
            fputcsv($handle, ['Total Customers', 'New Customers in Period', 'Active Customers', 'Period Orders', 'Period Revenue ($)', 'All-Time Revenue ($)']);
            fputcsv($handle, [
                $summary['total_customers'],
                $summary['new_customers_count'],
                $summary['active_customers_count'],
                $summary['period_orders_count'],
                number_format($summary['period_total_revenue'], 2),
                number_format($summary['all_time_revenue'], 2),
            ]);
            fputcsv($handle, []);

            // Data Table Headers
            fputcsv($handle, [
                '#',
                'Customer ID',
                'Full Name',
                'Email Address',
                'Phone Number',
                'Address',
                'Joined Date',
                'Period Orders',
                'Period Spent ($)',
                'Total Orders',
                'Total Spent ($)',
                'Last Order Date',
                'Status',
                'Source',
            ]);

            // Data Rows
            $index = 1;
            foreach ($customers as $c) {
                fputcsv($handle, [
                    $index++,
                    $c->id,
                    $c->name,
                    $c->email,
                    $c->phone,
                    $c->address,
                    $c->created_at ? $c->created_at->format('Y-m-d H:i') : 'N/A',
                    $c->period_orders_count,
                    number_format($c->period_total_spent, 2),
                    $c->total_orders,
                    number_format($c->total_spent, 2),
                    $c->last_order_date ? $c->last_order_date->format('Y-m-d H:i') : 'No Orders',
                    $c->status,
                    $c->source,
                ]);
            }

            // Totals Row
            fputcsv($handle, []);
            fputcsv($handle, [
                'TOTALS',
                '',
                'Total Customers: ' . $customers->count(),
                '',
                '',
                '',
                '',
                $summary['period_orders_count'],
                number_format($summary['period_total_revenue'], 2),
                '',
                number_format($summary['all_time_revenue'], 2),
                '',
                '',
                '',
            ]);

            fclose($handle);
        }, 200, $headers);
    }

    // ==========================================
    // Legacy Report CRUD Methods
    // ==========================================
    public function create()
    {
        return view('reports.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'generated_by' => 'required|string|max:255',
        ]);

        Report::create($request->only(['name', 'generated_by']));
        return redirect()->route('reports.index')->with('success', 'Report generated successfully!');
    }

    public function show($id)
    {
        $report = Report::findOrFail($id);
        return view('reports.show', compact('report'));
    }

    public function edit($id)
    {
        $report = Report::findOrFail($id);
        return view('reports.edit', compact('report'));
    }

    public function update(Request $request, $id)
    {
        $report = Report::findOrFail($id);
        $report->update($request->all());
        return redirect()->route('reports.index')->with('success', 'Report updated successfully');
    }

    public function destroy($id)
    {
        $report = Report::findOrFail($id);
        $report->delete();
        return redirect()->route('reports.index')->with('success', 'Report deleted successfully');
    }
}
