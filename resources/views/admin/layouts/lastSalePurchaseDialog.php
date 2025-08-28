<!-- Product Report Modal -->
    <div class="modal fade" id="productReportModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Product Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <!-- Purchases -->
                <h6 class="mb-2">Last 3 Purchases</h6>
                <table class="table table-bordered">
                <thead>
                    <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Unit Cost</th>
                    <th>Date</th>
                    </tr>
                </thead>
                <tbody id="purchaseData"></tbody>
                </table>

                <!-- Sales -->
                <h6 class="mt-4 mb-2">Last 3 Sales</h6>
                <table class="table table-bordered">
                <thead>
                    <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Sale Price</th>
                    <th>Customer</th>
                    <th>Date</th>
                    </tr>
                </thead>
                <tbody id="saleData"></tbody>
                </table>

            </div>
            </div>
        </div>
    </div>