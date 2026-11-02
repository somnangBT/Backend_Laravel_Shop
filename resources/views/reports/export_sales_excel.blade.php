<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    {!! '<!--[if gte mso 9]><xml><' . 'x:ExcelWorkbook><' . 'x:ExcelWorksheets><' . 'x:ExcelWorksheet><' . 'x:Name>Sales Report</' . 'x:Name><' . 'x:WorksheetOptions><' . 'x:DisplayGridlines/></' . 'x:WorksheetOptions></' . 'x:ExcelWorksheet></' . 'x:ExcelWorksheets></' . 'x:ExcelWorkbook></xml><![endif]-->' !!}
    <style>
        body {
            font-family: 'Segoe UI', Calibri, Arial, sans-serif;
            font-size: 11pt;
            color: #1e293b;
            background-color: #ffffff;
        }
        .report-title {
            font-size: 18pt;
            font-weight: bold;
            color: #0f172a;
            padding: 10px 0 2px 0;
        }
        .report-subtitle {
            font-size: 12pt;
            font-weight: bold;
            color: #059669;
            padding: 0 0 10px 0;
        }
        .meta-table td {
            font-size: 10pt;
            padding: 3px 6px;
        }
        .meta-label {
            font-weight: bold;
            color: #475569;
        }
        .section-header {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 11pt;
            font-weight: bold;
            padding: 8px 10px;
        }
        .kpi-table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }
        .kpi-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
            border: 1px solid #cbd5e1;
            padding: 8px;
            text-align: center;
        }
        .kpi-table td {
            border: 1px solid #cbd5e1;
            padding: 10px;
            font-size: 14pt;
            font-weight: bold;
            text-align: center;
            color: #0f172a;
        }
        .data-table {
            border-collapse: collapse;
            width: 100%;
        }
        .data-table th {
            background-color: #0284c7;
            color: #ffffff;
            font-size: 10pt;
            font-weight: bold;
            border: 1px solid #0369a1;
            padding: 8px 6px;
            text-align: left;
        }
        .data-table td {
            border: 1px solid #e2e8f0;
            padding: 6px;
            font-size: 10pt;
            vertical-align: middle;
        }
        .data-table tr.even {
            background-color: #f8fafc;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .total-row td {
            background-color: #f1f5f9;
            font-weight: bold;
            border-top: 2px solid #0f172a;
            border-bottom: 2px solid #0f172a;
            font-size: 11pt;
        }
        .currency {
            mso-number-format: "\$#,##0.00";
            text-align: right;
            font-weight: bold;
        }
        .number-format {
            mso-number-format: "#,##0";
            text-align: center;
        }
        .date-format {
            mso-number-format: "yyyy-mm-dd hh:mm";
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <table>
        <tr>
            <td colspan="12" class="report-title">DELICIOUSEATS — SALES & REVENUE REPORT</td>
        </tr>
        <tr>
            <td colspan="12" class="report-subtitle">
                Period: {{ $summary['period_label'] }} ({{ $summary['range_text'] }})
            </td>
        </tr>
    </table>

    <!-- Metadata Section -->
    <table class="meta-table">
        <tr>
            <td class="meta-label">Generated On:</td>
            <td>{{ $summary['generated_at']->format('Y-m-d H:i:s') }}</td>
            <td class="meta-label">Total Orders:</td>
            <td>{{ number_format($summary['total_orders']) }} orders</td>
        </tr>
        <tr>
            <td class="meta-label">Filter Status:</td>
            <td>{{ ucfirst($summary['selected_status']) }}</td>
            <td class="meta-label">Payment Filter:</td>
            <td>{{ strtoupper($summary['selected_payment']) }}</td>
        </tr>
    </table>

    <br>

    <!-- KPI Summary Section -->
    <table class="kpi-table">
        <tr>
            <th>Total Sales Revenue</th>
            <th>Paid Revenue</th>
            <th>Total Orders</th>
            <th>Completed Orders</th>
            <th>Pending Orders</th>
            <th>Cancelled Orders</th>
            <th>Average Order Value</th>
        </tr>
        <tr>
            <td style="color: #059669;">${{ number_format($summary['total_sales'], 2) }}</td>
            <td style="color: #0284c7;">${{ number_format($summary['paid_sales'], 2) }}</td>
            <td>{{ number_format($summary['total_orders']) }}</td>
            <td style="color: #059669;">{{ number_format($summary['completed_orders']) }}</td>
            <td style="color: #d97706;">{{ number_format($summary['pending_orders']) }}</td>
            <td style="color: #dc2626;">{{ number_format($summary['cancelled_orders']) }}</td>
            <td style="color: #7c3aed;">${{ number_format($summary['avg_order_value'], 2) }}</td>
        </tr>
    </table>

    <br>

    <!-- Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 40px;" class="text-center">#</th>
                <th style="width: 90px;" class="text-center">Order ID</th>
                <th style="width: 140px;" class="text-center">Date & Time</th>
                <th style="width: 180px;">Customer Name</th>
                <th style="width: 180px;">Email</th>
                <th style="width: 120px;">Phone</th>
                <th style="width: 80px;" class="text-center">Items</th>
                <th style="width: 100px;" class="text-right">Subtotal</th>
                <th style="width: 90px;" class="text-right">Discount</th>
                <th style="width: 90px;" class="text-right">Shipping</th>
                <th style="width: 110px;" class="text-right">Total ($)</th>
                <th style="width: 110px;" class="text-center">Payment</th>
                <th style="width: 100px;" class="text-center">Pay Status</th>
                <th style="width: 110px;" class="text-center">Order Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $index => $order)
                <tr class="{{ $index % 2 === 1 ? 'even' : '' }}">
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center" style="font-weight: bold;">#{{ $order->id }}</td>
                    <td class="date-format">{{ $order->created_at ? $order->created_at->format('Y-m-d H:i') : 'N/A' }}</td>
                    <td style="font-weight: bold;">{{ optional($order->user)->name ?? ($order->email ?: 'Guest') }}</td>
                    <td>{{ $order->email ?: optional($order->user)->email ?: 'N/A' }}</td>
                    <td>{{ $order->phone ?: 'N/A' }}</td>
                    <td class="number-format">{{ $order->orderItems ? $order->orderItems->sum('quantity') : 0 }}</td>
                    <td class="currency">{{ $order->subtotal }}</td>
                    <td class="currency">{{ $order->discount }}</td>
                    <td class="currency">{{ $order->shipping }}</td>
                    <td class="currency" style="color: #059669;">{{ $order->total }}</td>
                    <td class="text-center">{{ strtoupper($order->payment_method ?? 'CASH') }}</td>
                    <td class="text-center">{{ ucfirst($order->payment_status ?? 'pending') }}</td>
                    <td class="text-center" style="font-weight: bold;">{{ ucfirst($order->status ?? 'pending') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="14" class="text-center" style="padding: 20px; color: #64748b;">
                        No order records found for this period.
                    </td>
                </tr>
            @endforelse

            <!-- Total Row -->
            <tr class="total-row">
                <td colspan="6" class="text-right" style="font-weight: bold;">TOTAL SUMMARY:</td>
                <td class="number-format" style="font-weight: bold;">{{ $orders->sum(fn($o) => $o->orderItems ? $o->orderItems->sum('quantity') : 0) }}</td>
                <td class="currency">{{ $orders->sum('subtotal') }}</td>
                <td class="currency">{{ $orders->sum('discount') }}</td>
                <td class="currency">{{ $orders->sum('shipping') }}</td>
                <td class="currency" style="color: #059669; font-size: 12pt;">{{ $summary['total_sales'] }}</td>
                <td colspan="3"></td>
            </tr>
        </tbody>
    </table>

</body>
</html>
