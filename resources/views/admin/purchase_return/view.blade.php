@extends('admin.layouts.master')

@section('page-title')
    View Purchase Detail
@endsection
@section('main-content')
    <div class="dashboard-main-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">View Purchase Detail</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="{{ route('purchase.index') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        View Purchase Detail
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
                                <a href="{{ route('purchase.download',$result['purchase']->id) }}"
                                    class="btn btn-sm btn-warning radius-8 d-inline-flex align-items-center gap-1">
                                    <iconify-icon icon="solar:download-linear" class="text-xl"></iconify-icon>
                                    Download
                                </a>
                                <a href="{{ route('purchaseEdit',$result['purchase']->id) }}"
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
                                        <div class="p-20 d-flex flex-wrap justify-content-between gap-3 border-bottom">
                                            <div>
                                                <h3 class="text-xl">Invoice #{{ $result['purchase']->invoice_number }}</h3>
                                                <p class="mb-1 text-sm">Purchase Date: {{ $result['purchase']->purchase_date }}</p>
                                            </div>
                                           
                                        </div>
                                        <div class="py-28 px-20">
                                            <div class="d-flex flex-wrap justify-content-between align-items-end gap-3">
                                                <div>
                                                    <h6 class="text-md">Supplier Information:</h6>
                                                    <table class="text-sm text-secondary-light">
                                                        <tbody>
                                                            <tr>
                                                                <td>Name</td>
                                                                <td class="ps-8">:{{ (isset($result['purchase']->supplier_name))? $result['purchase']->supplier_name : '' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Address</td>
                                                                <td class="ps-8">:{{ (isset($result['purchase']->supplier_address))? $result['purchase']->supplier_address : '' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Phone number</td>
                                                                <td class="ps-8">:{{ (isset($result['purchase']->supplier_phone))? $result['purchase']->supplier_phone : '' }}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                        
                                            <div class="mt-24">
                                                <div class="table-responsive scroll-sm">
                                                    <table class="table bordered-table text-sm">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col" class="text-sm">SL.</th>
                                                                <th scope="col" class="text-sm">Items</th>
                                                                <th scope="col" class="text-sm">Qty</th>
                                                                <th scope="col" class="text-sm">Units</th>
                                                                <th scope="col" class="text-sm">Unit Price</th>
                                                                <th scope="col" class="text-end text-sm">Price</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($result['items'] as $key => $item)
                                                            {{-- {{ dd($item) }} --}}
                                                                <tr>
                                                                    <td>{{ $key+1 }}</td>
                                                                    <td>{{ $item->product_name }}</td>
                                                                    <td>{{ $item->quantity }}</td>
                                                                    <td>{{ $item->unit_name }}</td>
                                                                    <td>{{ $item->unit_cost }}</td>
                                                                    <td class="text-end">{{ $item->subtotal }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="d-flex flex-wrap justify-content-between gap-3">
                                                    <div>
                                                        {{-- <p class="text-sm mb-0"><span
                                                                class="text-primary-light fw-semibold">Sales By:</span>
                                                            Jammal</p>
                                                        <p class="text-sm mb-0">Thanks for your business</p> --}}
                                                    </div>
                                                    <div>
                                                        <table class="text-sm">
                                                            <tbody>
                                                                <tr>
                                                                    <td class="pe-64">Subtotal:</td>
                                                                    <td class="pe-16">
                                                                        <span
                                                                            class="text-primary-light fw-semibold">PKR{{ (isset($result['purchase']->total_amount))? $result['purchase']->total_amount : '' }}</span>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="pe-64">Discount:</td>
                                                                    <td class="pe-16">
                                                                        <span
                                                                            class="text-primary-light fw-semibold">PKR{{ (isset($result['purchase']->discount))? $result['purchase']->discount : '' }}</span>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="pe-64 border-bottom pb-4">Tax:</td>
                                                                    <td class="pe-16 border-bottom pb-4">
                                                                        <span
                                                                            class="text-primary-light fw-semibold">{{ (isset($result['purchase']->tax))? $result['purchase']->tax : '' }}</span>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="pe-64 border-bottom pb-4">Shipping:</td>
                                                                    <td class="pe-16 border-bottom pb-4">
                                                                        <span
                                                                            class="text-primary-light fw-semibold">{{ (isset($result['purchase']->shipping_charge))? $result['purchase']->shipping_charge : '' }}</span>
                                                                    </td>
                                                                <tr>
                                                                    <td class="pe-64 pt-4">
                                                                        <span
                                                                            class="text-primary-light fw-semibold">Total:</span>
                                                                    </td>
                                                                    <td class="pe-16 pt-4">
                                                                        <span
                                                                            class="text-primary-light fw-semibold">PKR{{ (isset($result['purchase']->grand_total))? number_format($result['purchase']->grand_total, 2, '.', '') : '' }}</span>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                        
                                            {{-- <div class="mt-64">
                                                <p class="text-center text-secondary-light text-sm fw-semibold">Thank you
                                                    for your purchase!</p>
                                            </div> --}}
                        
                                            {{-- <div class="d-flex flex-wrap justify-content-between align-items-end mt-64">
                                                <div class="text-sm border-top d-inline-block px-12">Signature of Customer
                                                </div>
                                                <div class="text-sm border-top d-inline-block px-12">Signature of Authorized
                                                </div>
                                            </div> --}}
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
    {{-- <script src="{{ asset('admin/myjs/purchase/purchase_view.js') }}"></script> --}}
@endsection
