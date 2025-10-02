<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice #{{ $result['sale']->invoice_number }}</title>
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
            border-bottom: 1px solid #000000;
            padding-bottom: 10px;
        }
        .title {
            font-size: 18px;
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
            padding: 0px;
            border: 1px solid #000000;
        }
        .invoice-table td {
            padding: 5px;
            border: 1px solid #000000;
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
            border-top: 1px solid #000000;
            font-weight: bold;
            padding-top: 10px;
        }
        .invoice-table {
    width: 100%;
    border-collapse: collapse;
}

.invoice-table th,
.invoice-table td {
    border: 1px solid #000;
    padding: 6px;
}
/* Apply borders for screen + print */
table.table-bordered, 
    table.table-bordered th, 
    table.table-bordered td {
        border: 1px solid black !important;
    }

    /* Ensure borders are visible in print */
    @media print {
        table.table-bordered, 
        table.table-bordered th, 
        table.table-bordered td {
            border: 1px solid black !important;
        }
    }

    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="invoice-header">
            <div class="p-20 gap-3 border-bottom" style="text-align: center !important;">
                <span class="title">Khanjee Beauty Mart</span>
                <br>
                <p>ph: 03128192613, 03432650990</br>
                1st Branch : Shop # 1, Yousaf Plaza 3rd floor boltan market karachi</br>
                2nd Branch : RJ Mall shop # LG 15 karachi</br>
                3rd Branch : Iqbal market shop # 39 Boltan market karachi</p>
             </div>
        </div>
        <table width="100%" style="border-collapse: collapse; margin-bottom: 15px;">
            <tr>
                <!-- Left Side: Invoice Info -->

                <td style="vertical-align: top; text-align: left; width:70%;">
                    <h3 style="margin: 0; padding: 0;">Customer Information</h3>
                    <table align="left">
                        <tr>
                            <td><strong>Name</strong></td>
                            <td>: {{ $result['sale']->customer_name ?? '' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Address</strong></td>
                            <td>: {{ $result['sale']->customer_address ?? '' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Phone number</strong></td>
                            <td>: {{ $result['sale']->customer_phone ?? '' }}</td>
                        </tr>
                    </table>
                </td>
                
        
                <!-- Right Side: Customer Info -->
                <td style="vertical-align: top; text-align: left; width: 30%;">
                    <div class="title">
                        Invoice #{{ $result['sale']->invoice_number }}
                    </div>
                    <div>
                        Sale Date: {{ \Carbon\Carbon::parse($result['sale']->sale_date)->format('d/m/Y') }}
                    </div>
                </td>
            </tr>
        </table>
        <div class="table-responsive scroll-sm">
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th>SL.</th>
                        <th>Items</th>
                        <th>Qty</th>
                        <th>Units</th>
                        <th>Sale Price</th>
                        <th class="text-right">Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($result['items'] as $key => $item)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{ $item->product_name }} - {{ substr($item->product_barcode, -4) }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->unit_name }}</td>
                        <td>{{ number_format($item->selling_unit_price, 2) }}</td>
                        <td class="text-right">{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table> 
        </div>
       
        <table style="width:100%; border-collapse: collapse; margin-top:15px; font-size:12px;">
            <tr>
                <!-- Left: Number of Items -->
                <td style="vertical-align: top; width:40%; padding-right:10px;">
                    <table style="border:1px solid #000; border-collapse: collapse; width:100%;">
                        <tbody>
                            <tr>
                                <td style="border:1px solid #000; padding:5px;">Total Number of Items:</td>
                                <td style="border:1px solid #000; padding:5px; text-align:center;">
                                    {{ $result['items']->count() }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
        
                <!-- Right: Totals -->
                <td style="vertical-align: top; width:60%;">
                    <table style="border:1px solid #000; border-collapse: collapse; width:100%;">
                        <tbody>
                            <tr>
                                <td style="border:1px solid #000; padding:5px;">Subtotal:</td>
                                <td style="border:1px solid #000; padding:5px; text-align:right;">
                                    {{ number_format($result['sale']->total_amount ?? 0, 2) }}
                                </td>
                            </tr>
                            <tr>
                                <td style="border:1px solid #000; padding:5px;">Discount:</td>
                                <td style="border:1px solid #000; padding:5px; text-align:right;">
                                    {{ number_format($result['sale']->discount ?? 0, 2) }}
                                </td>
                            </tr>
                            <tr class="d-none">
                                <td style="border:1px solid #000; padding:5px;">Tax:</td>
                                <td style="border:1px solid #000; padding:5px; text-align:right;">
                                    {{ number_format($result['sale']->tax ?? 0, 2) }}
                                </td>
                            </tr>
                            <tr>
                                <td style="border:1px solid #000; padding:5px;">Shipping:</td>
                                <td style="border:1px solid #000; padding:5px; text-align:right;">
                                    {{ number_format($result['sale']->shipping_charge ?? 0, 2) }}
                                </td>
                            </tr>
                            <tr>
                                <td style="border:1px solid #000; padding:5px;"><strong>Total:</strong></td>
                                <td style="border:1px solid #000; padding:5px; text-align:right;">
                                    <strong>PKR {{ number_format($result['sale']->grand_total ?? 0, 2) }}</strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
        
        
       
    </div>
</body>
</html>