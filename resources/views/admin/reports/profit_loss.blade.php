@extends('admin.layouts.master')

@section('page-title') Profit & Loss Report @endsection

@section('main-content')
<div class="dashboard-main-body">
    <div class="container">
        <h3 class="text-center mb-4">Profit & Loss Report</h3>

        <form method="GET" action="{{ route('reports.profitLoss') }}" class="row g-3 mb-4 justify-content-center">
            <div class="col-md-3">
                <label class="form-label">From</label>
                <input type="date" name="from" value="{{ \Carbon\Carbon::parse($from)->toDateString() }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">To</label>
                <input type="date" name="to" value="{{ \Carbon\Carbon::parse($to)->toDateString() }}" class="form-control">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary w-100">Filter</button>
            </div>
        </form>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted">({{ $sales_count }}) Sales</h6>
                        <h4 class="text-success fw-bold">${{ number_format($sales_total, 2) }}</h4>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted">({{ $purchase_count }}) Purchases</h6>
                        <h4 class="text-primary fw-bold">${{ number_format($purchase_total, 2) }}</h4>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted">({{ $sales_return_count }}) Sales Return</h6>
                        <h4 class="text-warning fw-bold">${{ number_format($sales_return_total, 2) }}</h4>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted">({{ $purchase_return_count }}) Purchases Return</h6>
                        <h4 class="text-warning fw-bold">${{ number_format($purchase_return_total, 2) }}</h4>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted">Expenses</h6>
                        <h4 class="text-danger fw-bold">${{ number_format($expenses_total, 2) }}</h4>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-center shadow-sm bg-light">
                    <div class="card-body">
                        <h6 class="text-muted">Revenue</h6>
                        <h4 class="text-success fw-bold">${{ number_format($revenue_total, 2) }}</h4>
                        <small class="text-muted">({{ '$'.number_format($sales_total,2) }} Sales - {{ '$'.number_format($sales_return_total,2) }} Sales Return)</small>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted">Profit Net (Using FIFO METHOD)</h6>
                        <h4 class="text-success fw-bold">${{ number_format($profit_fifo, 2) }}</h4>
                        <small class="text-muted">({{ '$'.number_format($revenue_total,2) }} Sales - {{ '$'.number_format($fifo_cost,2) }} Product Cost)</small>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted">Profit Net (Using Average Cost)</h6>
                        <h4 class="text-success fw-bold">${{ number_format($profit_avg, 2) }}</h4>
                        <small class="text-muted">({{ '$'.number_format($revenue_total,2) }} Sales - {{ '$'.number_format($avg_cost,2) }} Product Cost)</small>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted">Payments Received</h6>
                        <h4 class="text-success fw-bold">${{ number_format($payments_received, 2) }}</h4>
                        <small class="text-muted">({{ '$'.number_format($payments_received,2) }} Payments Sales + $0.00 Purchases Return)</small>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted">Payments Sent</h6>
                        <h4 class="text-danger fw-bold">${{ number_format($payments_sent, 2) }}</h4>
                        <small class="text-muted">({{ '$'.number_format($payments_sent,2) }} Payments Purchases + $0.00 Sales Return + {{ '$'.number_format($expenses_total,2) }} Expenses)</small>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted">Payments Net</h6>
                        <h4 class="fw-bold {{ $payments_net < 0 ? 'text-danger' : 'text-success' }}">${{ number_format($payments_net, 2) }}</h4>
                        <small class="text-muted">({{ '$'.number_format($payments_received,2) }} Received - {{ '$'.number_format($payments_sent,2) }} Sent)</small>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
