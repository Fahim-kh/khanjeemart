<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice #{{ $result['sale']->invoice_number }}</title>
    <style>
        @page { size: A4; margin: 15mm; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #000;
        }
        .invoice-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 0;
        }
        .invoice-header {
            margin-bottom: 12px;
            border-bottom: 1px solid #000;
            padding-bottom: 8px;
            text-align: center;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
        }

        /* Items table */
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .invoice-table th,
        .invoice-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: middle;
        }
        .invoice-table th {
            background-color: #f5f5f5;
            text-align: left;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* Small left table (number of items) */
        .count-table {
            width: 100%;
            border-collapse: collapse;
        }
        .count-table td {
            border: 1px solid #000;
            padding: 6px 8px;
        }

        /* Totals table on right */
        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals-table td {
            border: 1px solid #000;
            padding: 6px 8px;
        }

        /* Profit styling */
        .profit {
            background-color: #198754; /* green */
            color: #fff;
            font-weight: bold;
            text-align: right;
        }

        /* ensure tables print with borders */
        @media print {
            table, th, td { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="invoice-header">
            <div class="title">Khanjee Beauty Mart</div>
            <div style="font-size:11px; margin-top:6px;">
                ph: 03128192613, 03432650990<br>
                1st Branch: Shop #1, Yousaf Plaza 3rd floor Bolton Market Karachi<br>
                2nd Branch: RJ Mall shop # LG 15 Karachi<br>
                3rd Branch: Iqbal Market shop # 39 Bolton Market Karachi
            </div>
        </div>

        <!-- Customer & Invoice Info -->
        <table width="100%" style="border-collapse: collapse; margin-bottom: 10px;">
            <tr>
                <td style="vertical-align: top; width: 70%; padding-right:8px;">
                    <strong>Customer Information</strong>
                    <table style="border-collapse: collapse; margin-top:6px;">
                        <tr>
                            <td style="padding-right:8px;"><strong>Name</strong></td>
                            <td>: {{ $result['sale']->customer_name ?? '' }}</td>
                        </tr>
                        <tr>
                            <td style="padding-right:8px;"><strong>Address</strong></td>
                            <td>: {{ $result['sale']->customer_address ?? '' }}</td>
                        </tr>
                        <tr>
                            <td style="padding-right:8px;"><strong>Phone</strong></td>
                            <td>: {{ $result['sale']->customer_phone ?? '' }}</td>
                        </tr>
                    </table>
                </td>
                <td style="vertical-align: top; width: 30%; text-align: left;">
                    <div style="font-weight:700;">Invoice #{{ $result['sale']->invoice_number }}</div>
                    <div>Sale Date: {{ \Carbon\Carbon::parse($result['sale']->sale_date)->format('d/m/Y') }}</div>
                </td>
            </tr>
        </table>

        <!-- Items Table -->
        @php $overallProfit = 0; @endphp
        <table class="invoice-table">
            <thead>
                <tr>
                    <th style="width:4%;">SL.</th>
                    <th style="width:40%;">Items</th>
                    <th style="width:8%;">Qty</th>
                    <th style="width:10%;">Units</th>
                    <th style="width:12%;">Sale Price</th>
                    <th style="width:12%;">Cost Price</th>
                    <th style="width:10%;" class="text-right">Price</th>
                    <th style="width:10%;">Profit</th>
                </tr>
            </thead>
            <tbody>
                @foreach($result['items'] as $key => $item)
                    @php
                        $profit = ($item->selling_unit_price - $item->cost_unit_price) * $item->quantity;
                        $overallProfit += $profit;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $key + 1 }}</td>
                        <td>{{ $item->product_name }} - {{ substr($item->product_barcode, -4) }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-center">{{ $item->unit_name }}</td>
                        <td class="text-right">{{ number_format($item->selling_unit_price, 2) }}</td>
                        <td class="text-right">{{ number_format($item->cost_unit_price, 2) }}</td>
                        <td class="text-right">{{ number_format($item->subtotal, 2) }}</td>
                        <td class="profit">{{ number_format($profit, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Bottom row: left = number of items, right = totals (side by side using table layout for PDF compatibility) -->
        <table style="width:100%; border-collapse: collapse; margin-top:12px;">
            <tr>
                <!-- Left: Number of Items -->
                <td style="vertical-align: top; width:40%; padding-right:8px;">
                    <table class="count-table" style="width:100%;">
                        <tbody>
                            <tr>
                                <td style="width:70%;">Total Number of Items:</td>
                                <td style="width:30%; text-align:center;">{{ $result['items']->count() }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>

                <!-- Right: Totals -->
                <td style="vertical-align: top; width:60%;">
                    <table class="totals-table" style="width:100%;">
                        <tbody>
                            <tr>
                                <td style="width:60%;">Subtotal:</td>
                                <td class="text-right" style="width:40%;">{{ number_format($result['sale']->total_amount ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Discount:</td>
                                <td class="text-right">{{ number_format($result['sale']->discount ?? 0, 2) }}</td>
                            </tr>
                            <tr class="d-none">
                                <td>Tax:</td>
                                <td class="text-right">{{ number_format($result['sale']->tax ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Shipping:</td>
                                <td class="text-right">{{ number_format($result['sale']->shipping_charge ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total:</strong></td>
                                <td class="text-right"><strong>PKR {{ number_format($result['sale']->grand_total ?? 0, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <td class="profit"><strong>Total Profit:</strong></td>
                                <td class="profit"><strong>PKR {{ number_format($overallProfit ?? 0, 2) }}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
