<!DOCTYPE html>
<html>
<head>
    <title>Product Detail Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; position: relative; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background: #f0f0f0; }
        .header { text-align: center; margin-bottom: 20px; }
        .header strong { font-size: 18px; }
        .header p { margin: 2px 0; font-size: 12px; }
        .footer { position: fixed; bottom: 0; right: 0; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <strong>Khanjee Beauty Mart</strong><br>
        <p>
            ph: 03128192613, 03432650990 <br>
            1st Branch : Shop # 1, Yousaf Plaza 3rd floor boltan market karachi <br>
            2nd Branch : RJ Mall shop # LG 15 karachi <br>
            3rd Branch : Iqbal market shop # 39 Boltan market karachi
        </p>
    </div>

    <h2 style="text-align:center;">Product Sale Detail Report</h2>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Invoice #</th>
                <th>Created By</th>
                <th>Customer</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
                <tr>
                    <td>{{ $row->sale_date }}</td>
                    <td>{{ $row->invoice_number }}</td>
                    <td>{{ $row->created_by_name }}</td>
                    <td>{{ $row->customer_name }}</td>
                    <td>{{ $row->product_name }}</td>
                    <td>{{ $row->quantity }} {{ $row->unit_name }}</td>
                    <td>{{ number_format($row->selling_unit_price, 2) }}</td>
                    <td>{{ number_format($row->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        {{ date('Y-m-d') }}
    </div>
</body>
</html>
