<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    {!! '<!--[if gte mso 9]><xml><' . 'x:ExcelWorkbook><' . 'x:ExcelWorksheets><' . 'x:ExcelWorksheet><' . 'x:Name>Customer Report</' . 'x:Name><' . 'x:WorksheetOptions><' . 'x:DisplayGridlines/></' . 'x:WorksheetOptions></' . 'x:ExcelWorksheet></' . 'x:ExcelWorksheets></' . 'x:ExcelWorkbook></xml><![endif]-->' !!}
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
            color: #0284c7;
            padding: 0 0 10px 0;
        }
        .meta-table td {
            font-size: 10pt;
            padding: 3px 6px;
        }
        .meta-label {
            font-weight: bold;
            color: #475569;
            width: 140px;
        }
        .meta-val {
            color: #0f172a;
        }
        .kpi-table {
            border-collapse: collapse;
            margin: 15px 0 20px 0;
        }
        .kpi-table th {
            background-color: #0284c7;
            color: #ffffff;
            font-size: 10pt;
            font-weight: bold;
            text-align: center;
            padding: 8px 12px;
            border: 1px solid #0369a1;
        }
        .kpi-table td {
            background-color: #f0f9ff;
            color: #0369a1;
            font-size: 14pt;
            font-weight: bold;
            text-align: center;
            padding: 10px 12px;
            border: 1px solid #bae6fd;
        }
        .data-table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 15px;
        }
        .data-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 10pt;
            font-weight: bold;
            padding: 10px 8px;
            border: 1px solid #334155;
            text-align: left;
            vertical-align: middle;
        }
        .data-table td {
            padding: 8px;
            border: 1px solid #cbd5e1;
            font-size: 10pt;
            vertical-align: middle;
        }
        .data-table tr.even {
            background-color: #f8fafc;
        }
        .data-table tr.total-row td {
            background-color: #e2e8f0;
            font-weight: bold;
            border-top: 2px solid #0f172a;
            border-bottom: 2px solid #0f172a;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .num { mso-number-format: "\#\,\#\#0"; text-align: right; }
        .currency { mso-number-format: "\$\#\,\#\#0\.00"; text-align: right; }
        .date { mso-number-format: "yyyy-mm-dd hh\:mm"; text-align: center; }
        .status-active { background-color: #dcfce7; color: #166534; font-weight: bold; text-align: center; }
        .status-new { background-color: #e0e7ff; color: #3730a3; font-weight: bold; text-align: center; }
        .status-regular { background-color: #fef3c7; color: #92400e; text-align: center; }
        .status-inactive { background-color: #f1f5f9; color: #64748b; text-align: center; }
    </style>
</head>
<body>
    <!-- Title Banner -->
    <div class="report-title">DELICIOUSEATS - CUSTOMER DATA REPORT</div>
    <div class="report-subtitle">របាយការណ៍ទិន្នន័យអតិថិជន ({{ strtoupper($summary['period']) }})</div>

    <!-- Metadata -->
    <table class="meta-table" cellpadding="0" cellspacing="0">
        <tr>
            <td class="meta-label">Period Type:</td>
            <td class="meta-val"><b>{{ ucfirst($summary['period']) }}</b> ({{ $summary['period_label'] }})</td>
        </tr>
        <tr>
            <td class="meta-label">Date Range:</td>
            <td class="meta-val">{{ $summary['start_date']->format('M d, Y') }} — {{ $summary['end_date']->format('M d, Y') }}</td>
        </tr>
        <tr>
            <td class="meta-label">Exported Date:</td>
            <td class="meta-val">{{ $summary['generated_at']->format('M d, Y h:i A') }}</td>
        </tr>
        <tr>
            <td class="meta-label">Exported By:</td>
            <td class="meta-val">{{ auth()->user()->name ?? 'Administrator' }} ({{ auth()->user()->email ?? 'admin@system.local' }})</td>
        </tr>
    </table>

    <br>

    <!-- Summary KPI Box -->
    <table class="kpi-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th>Total Customers</th>
                <th>New Registered (Period)</th>
                <th>Active Customers</th>
                <th>Period Orders</th>
                <th>Period Revenue</th>
                <th>All-Time Revenue</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ number_format($summary['total_customers']) }}</td>
                <td>{{ number_format($summary['new_customers_count']) }}</td>
                <td>{{ number_format($summary['active_customers_count']) }}</td>
                <td>{{ number_format($summary['period_orders_count']) }}</td>
                <td>${{ number_format($summary['period_total_revenue'], 2) }}</td>
                <td>${{ number_format($summary['all_time_revenue'], 2) }}</td>
            </tr>
        </tbody>
    </table>

    <br>

    <!-- Customer Details Table -->
    <table class="data-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width: 40px;" class="text-center">#</th>
                <th style="width: 90px;">ID</th>
                <th style="width: 180px;">Customer Name</th>
                <th style="width: 200px;">Email</th>
                <th style="width: 120px;">Phone</th>
                <th style="width: 220px;">Address</th>
                <th style="width: 140px;" class="text-center">Joined Date</th>
                <th style="width: 100px;" class="text-right">Period Orders</th>
                <th style="width: 120px;" class="text-right">Period Spent</th>
                <th style="width: 100px;" class="text-right">Total Orders</th>
                <th style="width: 120px;" class="text-right">All-Time Spent</th>
                <th style="width: 140px;" class="text-center">Last Order</th>
                <th style="width: 90px;" class="text-center">Status</th>
                <th style="width: 110px;" class="text-center">Source</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $index => $c)
                <tr class="{{ $index % 2 === 1 ? 'even' : '' }}">
                    <td class="text-center num">{{ $index + 1 }}</td>
                    <td>{{ $c->id }}</td>
                    <td><b>{{ $c->name }}</b></td>
                    <td>{{ $c->email }}</td>
                    <td>{{ $c->phone }}</td>
                    <td>{{ $c->address }}</td>
                    <td class="date">{{ $c->created_at ? $c->created_at->format('Y-m-d H:i') : 'N/A' }}</td>
                    <td class="num">{{ $c->period_orders_count }}</td>
                    <td class="currency">${{ number_format($c->period_total_spent, 2) }}</td>
                    <td class="num">{{ $c->total_orders }}</td>
                    <td class="currency">${{ number_format($c->total_spent, 2) }}</td>
                    <td class="date">{{ $c->last_order_date ? $c->last_order_date->format('Y-m-d H:i') : 'No Orders' }}</td>
                    <td class="{{ $c->status === 'Active' ? 'status-active' : ($c->status === 'New' ? 'status-new' : ($c->status === 'Regular' ? 'status-regular' : 'status-inactive')) }}">
                        {{ $c->status }}
                    </td>
                    <td class="text-center">{{ $c->source }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="14" class="text-center" style="padding: 20px; color: #64748b;">
                        No customer data found for the selected timeframe.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($customers->count() > 0)
            <tfoot>
                <tr class="total-row">
                    <td colspan="6" class="text-right"><b>TOTALS / សរុបរួម:</b></td>
                    <td class="text-center"><b>{{ $customers->count() }} Customers</b></td>
                    <td class="num"><b>{{ $summary['period_orders_count'] }}</b></td>
                    <td class="currency"><b>${{ number_format($summary['period_total_revenue'], 2) }}</b></td>
                    <td class="num"><b>{{ $customers->sum('total_orders') }}</b></td>
                    <td class="currency"><b>${{ number_format($summary['all_time_revenue'], 2) }}</b></td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        @endif
    </table>
</body>
</html>
