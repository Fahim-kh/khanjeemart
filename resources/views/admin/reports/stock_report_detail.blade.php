@extends('admin.layouts.master')

@section('page-title')
    Stock Report Detail
@endsection

@section('main-content')
    <div class="dashboard-main-body">
        <h4>Stock Report Detail - {{ $product->name }} ({{ $product->barcode }})</h4>

        <ul class="nav nav-tabs" id="stockTabs">
            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tabPurchase">Purchase</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabPurchaseReturn">Purchase Return</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabSale">Sale</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabSaleReturn">Sale Return</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabAdjustment">Adjustment</a></li>
        </ul>

        <div class="tab-content mt-3">
            <div class="tab-pane fade show active" id="tabPurchase">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped w-100" id="tblPurchase">
                        <thead>
                            <tr>
                                <th class="text-start">Date</th>
                                <th>Reference</th>
                                <th>Supplier</th>
                                <th>Product</th>
                                <th class="text-start">Qty</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="tabPurchaseReturn">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped w-100" id="tblPurchaseReturn">
                        <thead>
                            <tr>
                                <th class="text-start">Date</th>
                                <th>Reference</th>
                                <th>Supplier</th>
                                <th>Product</th>
                                <th class="text-start">Qty</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="tabSale">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped w-100" id="tblSale">
                        <thead>
                            <tr>
                                <th class="text-start">Date</th>
                                <th>Reference</th>
                                <th>Customer</th>
                                <th>Product</th>
                                <th class="text-start">Qty</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="tabSaleReturn">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped w-100" id="tblSaleReturn">
                        <thead>
                            <tr>
                                <th class="text-start">Date</th>
                                <th>Reference</th>
                                <th>Customer</th>
                                <th>Product</th>
                                <th class="text-start">Qty</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="tabAdjustment">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped w-100" id="tblAdjustment">
                        <thead>
                            <tr>
                                <th class="text-start">Date</th>
                                <th class="text-start">ID</th>
                                <th>Product</th>
                                <th>Type</th>
                                <th class="text-start">Qty</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function () {
            $('#tblPurchase').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('stock.report.purchase', $product->id) }}",
                columns: [
                    { data: 'date', name: 'p.purchase_date' },
                    { data: 'reference', name: 'p.invoice_number' },
                    { data: 'supplier_name', name: 's.name' },
                    { data: 'product_name', name: 'pr.name' },
                    { data: 'quantity', name: 'pi.quantity' }
                ]
            });

            $('#tblPurchaseReturn').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('stock.report.purchase.return', $product->id) }}",
                columns: [
                    { data: 'date', name: 'p.purchase_date' },
                    { data: 'reference', name: 'p.invoice_number' },
                    { data: 'supplier_name', name: 's.name' },
                    { data: 'product_name', name: 'pr.name' },
                    { data: 'quantity', name: 'pi.quantity' }
                ]
            });

            $('#tblSale').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: "{{ route('stock.report.sale', $product->id) }}",
                columns: [
                    { data: 'date', name: 'ss.sale_date' },
                    { data: 'reference', name: 'ss.invoice_number' },
                    { data: 'customer_name', name: 'c.name' },
                    { data: 'product_name', name: 'p.name' },
                    { data: 'quantity', name: 'sd.quantity' }
                ]
            });

            $('#tblSaleReturn').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: "{{ route('stock.report.sale.return', $product->id) }}",
                columns: [
                    { data: 'date', name: 'ss.sale_date' },
                    { data: 'reference', name: 'ss.invoice_number' },
                    { data: 'customer_name', name: 'c.name' },
                    { data: 'product_name', name: 'p.name' },
                    { data: 'quantity', name: 'sd.quantity' }
                ]
            });

            // Adjustment
            $('#tblAdjustment').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('stock.report.adjustment', $product->id) }}",
                columns: [
                    { data: 'date', name: 'sa.adjustment_date' },
                    { data: 'reference', name: 'sa.id' },
                    { data: 'product_name', name: 'pr.name' },
                    { data: 'adjustment_type', name: 'sai.adjustment_type' },
                    { data: 'quantity', name: 'sai.quantity' }
                ]
            });
        });
    </script>
@endsection