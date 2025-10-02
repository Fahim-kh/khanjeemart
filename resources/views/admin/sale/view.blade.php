@extends('admin.layouts.master')

@section('page-title')
    View Sale Detail
@endsection
@section('main-content')
    <div class="dashboard-main-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">View Sale Detail</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="{{ route('sale.index') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        View Sale Detail
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium">Dashboard</li>
            </ul>
        </div>
        <div class="container">
            @include('admin.layouts.errorLayout')
            @include('admin.layouts.successLayout')
            <div class="row justify-content-center ">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex flex-wrap align-items-center justify-content-end gap-2">
                                {{-- <a href="javascript:void(0)"
                                    class="btn btn-sm btn-primary-600 radius-8 d-inline-flex align-items-center gap-1">
                                    <iconify-icon icon="pepicons-pencil:paper-plane" class="text-xl"></iconify-icon>
                                    Send Invoice
                                </a> --}}
                                <a href="{{ route('sale.download',$result['sale']->id) }}"
                                    class="btn btn-sm btn-warning radius-8 d-inline-flex align-items-center gap-1">
                                    <iconify-icon icon="solar:download-linear" class="text-xl"></iconify-icon>
                                    Download
                                </a>
                                <a href="{{ route('saleEdit',$result['sale']->id) }}"
                                    class="btn btn-sm btn-success radius-8 d-inline-flex align-items-center gap-1">
                                    <iconify-icon icon="uil:edit" class="text-xl"></iconify-icon>
                                    Edit
                                </a>
                                <button type="button"
                                    class="btn btn-sm btn-danger radius-8 d-inline-flex align-items-center gap-1"
                                    onclick="printInvoice()">
                                    <iconify-icon icon="basil:printer-outline" class="text-xl"></iconify-icon>
                                    Print
                                </button>
                            </div>
                        </div>
                        {{-- invoice here --}}
                        <div class="card-body py-40">
                            <div class="row justify-content-center" id="invoice">
                                <div class="col-lg-12">
                                    <div class="shadow-4 border radius-8">
                                        <div class="p-20 gap-3 border-bottom" style="text-align: center !important;">
                                           <strong>Khanjee Beauty Mart</strong>
                                           <br>
                                           <p>ph: 03128192613, 03432650990</br>
                                           1st Branch : Shop # 1, Yousaf Plaza 3rd floor boltan market karachi</br>
                                           2nd Branch : RJ Mall shop # LG 15 karachi</br>
                                           3rd Branch : Iqbal market shop # 39 Boltan market karachi</p>
                                        </div>
                                        <div class="py-28 px-20">
                                            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                                                <div>
                                                    <h3 class="text-xl">Invoice #{{ $result['sale']->invoice_number }}</h3>
                                                    <p class="mb-1 text-sm">Sale Date: {{ \Carbon\Carbon::parse($result['sale']->sale_date)->format('Y-m-d') }}</p>
                                                </div>
                                                <div>
                                                    <h6 class="text-md">Customer Information:</h6>
                                                    <table class="text-sm text-secondary-light">
                                                        <tbody>
                                                            <tr>
                                                                <td>Name</td>
                                                                @php
                                                                    $customerStatus = null;
                                                                    if ($result['sale']->customer_status == 1) {
                                                                        $customerStatus = '(Owner)';
                                                                    } else {
                                                                        $customerStatus = null; // or leave as-is
                                                                    }
                                                                @endphp
                                                                    
                                                                <td class="ps-8">:{{ (isset($result['sale']->customer_name))? $result['sale']->customer_name  .' '. $customerStatus : '' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Address</td>
                                                                <td class="ps-8">:{{ (isset($result['sale']->customer_address))? $result['sale']->customer_address : '' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Phone number</td>
                                                                <td class="ps-8">:{{ (isset($result['sale']->customer_phone))? $result['sale']->customer_phone : '' }}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                
                                            </div>
                        
                                            <div class="mt-24">
                                                <div class="table-responsive scroll-sm">
                                                    <table class="table table-bordered invoice-table text-sm" style="border:1px solid #000; border-collapse: collapse; width:100%;">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col" class="text-sm">SL.</th>
                                                                <th scope="col" class="text-sm">Items</th>
                                                                <th scope="col" class="text-sm">Qty</th>
                                                                <th scope="col" class="text-sm">Units</th>
                                                                <th scope="col" class="text-sm">Sale Price</th>
                                                                <th scope="col" class="text-end text-sm">Price</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($result['items'] as $key => $item)
                                                            <tr>
                                                                <td>{{ $key+1 }}</td>
                                                                <td>{{ $item->product_barcode }}-{{ $item->product_name }}</td>
                                                                <td>{{ $item->quantity }}</td>
                                                                <td>{{ $item->unit_name }}</td>
                                                                <td>{{ $item->selling_unit_price }}</td>
                                                                <td class="text-end">{{ $item->subtotal }}</td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="d-flex flex-wrap justify-content-between gap-3">
                                                    <div>
                                                        <table class="text-sm" style="border:1px solid #000; border-collapse: collapse; width:100%;">
                                                            <tbody>
                                                                <tr>
                                                                    <td class="pe-64" style="border:1px solid #000; padding:3px;">Total Numbers Items:</td>
                                                                    <td class="pe-16" style="border:1px solid #000;padding:3px;     left: 5px; position: relative; ">
                                                                           {{ $result['items']->count() }}
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div>
                                                        <table class="text-sm" style="border:1px solid #000; border-collapse: collapse; width:100%;">
                                                            <tbody>
                                                                <tr>
                                                                    <td class="pe-64" style="border:1px solid #000; padding:3px;">Subtotal:</td>
                                                                    <td class="pe-16" style="border:1px solid #000;padding:3px; ">
                                                                        <span class="text-primary-light fw-semibold">
                                                                            PKR {{ (isset($result['sale']->total_amount))? $result['sale']->total_amount : '' }}
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="pe-64" style="border:1px solid #000; padding:3px;">Discount:</td>
                                                                    <td class="pe-16" style="border:1px solid #000; padding:3px;">
                                                                        <span class="text-primary-light fw-semibold">
                                                                            PKR {{ (isset($result['sale']->discount))? $result['sale']->discount : '' }}
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                                <tr class="d-none">
                                                                    <td class="pe-64 pb-4" style="border:1px solid #000;padding:3px;">Tax:</td>
                                                                    <td class="pe-16 pb-4" style="border:1px solid #000; padding:3px;">
                                                                        <span class="text-primary-light fw-semibold">
                                                                            {{ (isset($result['sale']->tax))? $result['sale']->tax : '' }}
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="pe-64 pb-4" style="border:1px solid #000; padding:3px;">Shipping:</td>
                                                                    <td class="pe-16 pb-4" style="border:1px solid #000; padding:3px;">
                                                                        <span class="text-primary-light fw-semibold">
                                                                            {{ (isset($result['sale']->shipping_charge))? $result['sale']->shipping_charge : '' }}
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="pe-64 pt-4" style="border:1px solid #000; padding:3px;">
                                                                        <span class="text-primary-light fw-semibold">Total:</span>
                                                                    </td>
                                                                    <td class="pe-16 pt-4" style="border:1px solid #000; padding:3px;">
                                                                        <span class="text-primary-light fw-semibold">
                                                                            PKR {{ (isset($result['sale']->grand_total))? number_format($result['sale']->grand_total, 2, '.', '') : '' }}
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
    @media print {
    body * {
        visibility: hidden;
    }
    #invoice, #invoice * {
        visibility: visible;
    }
    #invoice {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
}
    </style>
@endsection

@section('script')
<script>
    function printInvoice() {
        var printContents = document.getElementById('invoice').innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;

        window.print();

        document.body.innerHTML = originalContents;
    } 
</script>
@endsection
