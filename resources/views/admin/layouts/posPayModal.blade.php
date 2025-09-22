<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold" id="paymentModalLabel">Payment</h5>
                    <h5 class="ms-auto text-success fw-bold">
                        {{ env('CURRENCY_SYMBLE') }} 
                        <span id="payAbleGrandtotal" class="payAbleGrandtotal">00.00</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <!-- Customer -->
                    <h4 class="text-center mb-4 fw-semibold customername">Customer Name</h4>

                    <!-- Payment Info -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card text-center border-0 shadow-sm">
                                <div class="card-body">
                                    <p class="mb-1 text-muted">Total Paying</p>
                                    <h5 class="fw-bold text-success " >{{ env('CURRENCY_SYMBLE') }} <span class="payingTotal" id="payingTotal">00.00</span></h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center border-0 shadow-sm">
                                <div class="card-body">
                                    <p class="mb-1 text-muted">Balance</p>
                                    <h5 class="fw-bold"> {{ env('CURRENCY_SYMBLE') }}  <span id="balanceTotal" class="balanceTotal">00.00</span></h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center border-0 shadow-sm">
                                <div class="card-body">
                                    <p class="mb-1 text-muted">Change Return</p>
                                    <h5 class="fw-bold"> {{ env('CURRENCY_SYMBLE') }} <span class="changeReturn text-danger" id="changeReturn">0.00</span></h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Section -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <h6 class="fw-semibold mb-3">Payment #1</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Paying Amount *</label>
                                    <input type="text" class="form-control payingAmount" value="20">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Payment Choice *</label>
                                    <select class="form-select">
                                        <option selected>Cash</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tendered & Keypad -->
                    <div class="mb-4">
                        <h6 class="fw-semibold mb-3">Popular Tendered</h6>
                        <div class="d-flex flex-wrap gap-2 mb-3 quick-amounts">
                            <button class="btn btn-outline-secondary" data-amount="10.00">{{ env('CURRENCY_SYMBLE') }} 10.00</button>
                            <button class="btn btn-outline-secondary" data-amount="20.00">{{ env('CURRENCY_SYMBLE') }} 20.00</button>
                            <button class="btn btn-outline-secondary" data-amount="50.00">{{ env('CURRENCY_SYMBLE') }} 50.00</button>
                            <button class="btn btn-outline-secondary" data-amount="100.00">{{ env('CURRENCY_SYMBLE') }} 100.00</button>
                            <button class="btn btn-outline-secondary" data-amount="500.00">{{ env('CURRENCY_SYMBLE') }} 500.00</button>
                            <button class="btn btn-outline-secondary" data-amount="1000.00">{{ env('CURRENCY_SYMBLE') }} 1000.00</button>
                            <button class="btn btn-outline-secondary" data-amount="5000.00">{{ env('CURRENCY_SYMBLE') }} 5000.00</button>
                        </div>
                        {{-- cal here --}}
                    </div>
 {{-- <div class="row g-2">
                            <div class="col-4"><button class="btn btn-dark w-100">1</button></div>
                            <div class="col-4"><button class="btn btn-dark w-100">2</button></div>
                            <div class="col-4"><button class="btn btn-dark w-100">3</button></div>
                            <div class="col-4"><button class="btn btn-dark w-100">4</button></div>
                            <div class="col-4"><button class="btn btn-dark w-100">5</button></div>
                            <div class="col-4"><button class="btn btn-dark w-100">6</button></div>
                            <div class="col-4"><button class="btn btn-dark w-100">7</button></div>
                            <div class="col-4"><button class="btn btn-dark w-100">8</button></div>
                            <div class="col-4"><button class="btn btn-dark w-100">9</button></div>
                            <div class="col-4"><button class="btn btn-danger w-100">Clear</button></div>
                            <div class="col-4"><button class="btn btn-dark w-100">0</button></div>
                        </div> --}}
                    <!-- Notes & Account -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Sale notes</label>
                            <textarea class="form-control" rows="2"></textarea>
                        </div>
                    </div>

                    <div class="row mb-4">
                        {{-- <div class="col-md-6">
                            <label class="form-label">Account</label>
                            <select class="form-select">
                                <option>Choose Account</option>
                                <option>Account 1</option>
                                <option>Account 2</option>
                            </select>
                        </div> --}}
                        {{-- <div class="col-md-6 d-flex align-items-end gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="sendEmail">
                                <label class="form-check-label" for="sendEmail">Send Email</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="sendSMS">
                                <label class="form-check-label" for="sendSMS">Send SMS</label>
                            </div>
                        </div> --}}
                    </div>

                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-success w-100 py-2 fw-bold btnFinalSave enterButtonActive2">Pay</button>
                </div>
            </div>
        </div>
    </div>