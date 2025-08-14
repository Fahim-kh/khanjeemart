<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice #{{ $result['purchase']->invoice_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.5;
        }
        .invoice-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .invoice-header {
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .supplier-info {
            margin-bottom: 20px;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .invoice-table th {
            background-color: #f5f5f5;
            text-align: left;
            padding: 8px;
            border: 1px solid #ddd;
        }
        .invoice-table td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals-table {
            width: 300px;
            margin-left: auto;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 5px 10px;
        }
        .totals-table tr:last-child td {
            border-top: 1px solid #ddd;
            font-weight: bold;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="invoice-header">
            <div class="invoice-title">Invoice #{{ $result['purchase']->invoice_number }}</div>
            <div>Purchase Date: {{ $result['purchase']->purchase_date }}</div>
        </div>

        <div class="supplier-info">
            <h3>Supplier Information:</h3>
            <table>
                <tr>
                    <td><strong>Name</strong></td>
                    <td>: {{ $result['purchase']->supplier_name ?? '' }}</td>
                </tr>
                <tr>
                    <td><strong>Address</strong></td>
                    <td>: {{ $result['purchase']->supplier_address ?? '' }}</td>
                </tr>
                <tr>
                    <td><strong>Phone number</strong></td>
                    <td>: {{ $result['purchase']->supplier_phone ?? '' }}</td>
                </tr>
            </table>
        </div>

        <table class="invoice-table">
            <thead>
                <tr>
                    <th>SL.</th>
                    <th>Items</th>
                    <th>Qty</th>
                    <th>Units</th>
                    <th>Unit Price</th>
                    <th class="text-right">Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($result['items'] as $key => $item)
                <tr>
                    <td>{{ $key+1 }}</td>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->unit_name }}</td>
                    <td>{{ number_format($item->unit_cost, 2) }}</td>
                    <td class="text-right">{{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <table class="totals-table">
            <tr>
                <td>Subtotal:</td>
                <td class="text-right">{{ number_format($result['purchase']->total_amount ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>Discount:</td>
                <td class="text-right">{{ number_format($result['purchase']->discount ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>Tax:</td>
                <td class="text-right">{{ number_format($result['purchase']->tax ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>Shipping:</td>
                <td class="text-right">{{ number_format($result['purchase']->shipping_charge ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Total:</strong></td>
                <td class="text-right"><strong>PKR{{ number_format($result['purchase']->grand_total ?? 0, 2) }}</strong></td>
            </tr>
        </table>
    </div>
</body>
</html>