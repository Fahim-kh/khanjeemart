@extends('admin.layouts.master')

@section('page-title') Profit & Loss Report @endsection

@section('main-content')
<div class="dashboard-main-body">
    <div class="container">
        <div class="card">
            <div class="card-body pb-5">
                <h4 class="text-center mb-4">Profit & Loss Report</h4>

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

                <div class="row g-4 mt-4">

                    {{-- Sales --}}
                    <div class="col-xxl-4 col-xl-4 col-sm-6">
                        <div class="px-20 py-16 shadow-none radius-8 h-100 gradient-deep-1 left-line line-bg-primary position-relative overflow-hidden">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                                <div>
                                    <span class="mb-2 fw-medium text-secondary-light text-md">({{ $sales_count }}) Sales</span>
                                    <h6 class="fw-semibold mb-1 text-success">{{ env('CURRENCY_SYMBLE') }}{{ number_format($sales_total, 2) }}</h6>
                                </div>
                                <span class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-2xl mb-12 bg-primary-100 text-primary-600">
                                    <i class="ri-shopping-cart-fill"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Purchases --}}
                    <div class="col-xxl-4 col-xl-4 col-sm-6">
                        <div class="px-20 py-16 shadow-none radius-8 h-100 gradient-deep-2 left-line line-bg-info position-relative overflow-hidden">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                                <div>
                                    <span class="mb-2 fw-medium text-secondary-light text-md">({{ $purchase_count }}) Purchases</span>
                                    <h6 class="fw-semibold mb-1 text-primary">{{ env('CURRENCY_SYMBLE') }}{{ number_format($purchase_total, 2) }}</h6>
                                </div>
                                <span class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-2xl mb-12 bg-info-100 text-info-600">
                                    <i class="ri-handbag-fill"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Sales Return --}}
                    <div class="col-xxl-4 col-xl-4 col-sm-6">
                        <div class="px-20 py-16 shadow-none radius-8 h-100 gradient-deep-3 left-line line-bg-warning position-relative overflow-hidden">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                                <div>
                                    <span class="mb-2 fw-medium text-secondary-light text-md">({{ $sales_return_count }}) Sales Return</span>
                                    <h6 class="fw-semibold mb-1 text-warning">{{ env('CURRENCY_SYMBLE') }}{{ number_format($sales_return_total, 2) }}</h6>
                                </div>
                                <span class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-2xl mb-12 bg-warning-100 text-warning-600">
                                    <i class="ri-arrow-go-back-fill"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Purchase Return --}}
                    <div class="col-xxl-4 col-xl-4 col-sm-6">
                        <div class="px-20 py-16 shadow-none radius-8 h-100 gradient-deep-4 left-line line-bg-success position-relative overflow-hidden">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                                <div>
                                    <span class="mb-2 fw-medium text-secondary-light text-md">({{ $purchase_return_count }}) Purchase Return</span>
                                    <h6 class="fw-semibold mb-1 text-info">{{ env('CURRENCY_SYMBLE') }}{{ number_format($purchase_return_total, 2) }}</h6>
                                </div>
                                <span class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-2xl mb-12 bg-success-100 text-success-600">
                                    <i class="ri-refresh-fill"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Expenses --}}
                    <div class="col-xxl-4 col-xl-4 col-sm-6">
                        <div class="px-20 py-16 shadow-none radius-8 h-100 gradient-deep-5 left-line line-bg-danger position-relative overflow-hidden" style="background:#f8d7da;">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                                <div>
                                    <span class="mb-2 fw-medium text-secondary-light text-md">Expenses</span>
                                    <h6 class="fw-semibold mb-1 text-danger">{{ env('CURRENCY_SYMBLE') }}{{ number_format($expenses_total, 2) }}</h6>
                                </div>
                                <span class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-2xl mb-12 bg-danger-100 text-danger-600">
                                    <i class="ri-wallet-3-fill"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Revenue --}}
                    <div class="col-xxl-4 col-xl-4 col-sm-6">
                        <div class="px-20 py-16 shadow-none radius-8 h-100 gradient-deep-1 left-line line-bg-success position-relative overflow-hidden">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                                <div>
                                    <span class="mb-2 fw-medium text-secondary-light text-md">Revenue</span>
                                    <h6 class="fw-semibold mb-1 text-success">{{ env('CURRENCY_SYMBLE') }}{{ number_format($revenue_total, 2) }}</h6>
                                    <small class="text-muted">({{ env('CURRENCY_SYMBLE') }} {{ number_format($sales_total,2) }} Sales - {{ env('CURRENCY_SYMBLE') }} {{ number_format($sales_return_total,2) }} Sales Return)</small>
                                </div>
                                <span class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-2xl mb-12 bg-success-100 text-success-600">
                                    <i class="ri-bar-chart-box-fill"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Profit FIFO --}}
                    <div class="col-xxl-6 col-xl-6 col-sm-12">
                        <div class="px-20 py-16 shadow-none radius-8 h-100 gradient-deep-2 left-line line-bg-success position-relative overflow-hidden">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                                <div>
                                    <span class="mb-2 fw-medium text-secondary-light text-md">Profit Net (FIFO)</span>
                                    <h6 class="fw-semibold mb-1 text-success">{{ env('CURRENCY_SYMBLE') }}{{ number_format($profit_fifo, 2) }}</h6>
                                    <small class="text-muted">({{ env('CURRENCY_SYMBLE') }} {{ number_format($revenue_total,2) }} - {{ env('CURRENCY_SYMBLE') }} {{ number_format($fifo_cost,2) }})</small>
                                </div>
                                <span class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-2xl mb-12 bg-success-100 text-success-600">
                                    <i class="ri-line-chart-fill"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Profit Average --}}
                    <div class="col-xxl-6 col-xl-6 col-sm-12">
                        <div class="px-20 py-16 shadow-none radius-8 h-100 gradient-deep-3 left-line line-bg-success position-relative overflow-hidden">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                                <div>
                                    <span class="mb-2 fw-medium text-secondary-light text-md">Profit Net (Average Cost)</span>
                                    <h6 class="fw-semibold mb-1 text-success">{{ env('CURRENCY_SYMBLE') }}{{ number_format($profit_avg, 2) }}</h6>
                                    <small class="text-muted">({{ env('CURRENCY_SYMBLE') }} {{ number_format($revenue_total,2) }} - {{ env('CURRENCY_SYMBLE') }} {{ number_format($avg_cost,2) }})</small>
                                </div>
                                <span class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-2xl mb-12 bg-success-100 text-success-600">
                                    <i class="ri-line-chart-line"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Payments Received --}}
                    <div class="col-xxl-4 col-xl-4 col-sm-6">
                        <div class="px-20 py-16 shadow-none radius-8 h-100 gradient-deep-4 left-line line-bg-success position-relative overflow-hidden">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                                <div>
                                    <span class="mb-2 fw-medium text-secondary-light text-md">Payments Received</span>
                                    <h6 class="fw-semibold mb-1 text-success">{{ env('CURRENCY_SYMBLE') }}{{ number_format($payments_received, 2) }}</h6>
                                </div>
                                <span class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-2xl mb-12 bg-success-100 text-success-600">
                                    <i class="ri-bank-card-fill"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Payments Sent --}}
                    <div class="col-xxl-4 col-xl-4 col-sm-6">
                        <div class="px-20 py-16 shadow-none radius-8 h-100 gradient-deep-5 left-line line-bg-danger position-relative overflow-hidden" style="background:#f8d7da;">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                                <div>
                                    <span class="mb-2 fw-medium text-secondary-light text-md">Payments Sent</span>
                                    <h6 class="fw-semibold mb-1 text-danger">{{ env('CURRENCY_SYMBLE') }}{{ number_format($payments_sent, 2) }}</h6>
                                </div>
                                <span class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-2xl mb-12 bg-danger-100 text-danger-600">
                                    <i class="ri-exchange-dollar-fill"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Payments Net --}}
                    <div class="col-xxl-4 col-xl-4 col-sm-6">
                        <div class="px-20 py-16 shadow-none radius-8 h-100 gradient-deep-1 left-line line-bg-primary position-relative overflow-hidden">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                                <div>
                                    <span class="mb-2 fw-medium text-secondary-light text-md">Payments Net</span>
                                    <h6 class="fw-semibold mb-1 {{ $payments_net < 0 ? 'text-danger' : 'text-success' }}">
                                        {{ env('CURRENCY_SYMBLE') }} {{ number_format($payments_net, 2) }}
                                    </h6>
                                </div>
                                <span class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-2xl mb-12 bg-primary-100 text-primary-600">
                                    <i class="ri-money-dollar-circle-fill"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                </div> {{-- row --}}
            </div>
        </div>
    </div>
</div>
@endsection
