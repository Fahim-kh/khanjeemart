<style id="printStyles">
    .__web-inspector-hide-shortcut__,
    .__web-inspector-hide-shortcut__ * {
        visibility: hidden !important;
    }

@media print {

.change {
    font-size: 10px !important;
}

* {
    font-size: 12px;
    line-height: 18px;
}

body {
    margin: 0.5cm;
    margin-bottom: 1.6cm;
}

td,
th {
    padding: 1px 0;
}

#invoice-POS table tr {
    border-bottom: 2px dotted #05070b;
}

.invoice_logo {
    text-align: center;
}


@page {
    margin: 0;
}

}

.pos_page {
position: absolute;
top: 0;
right: 0;
width: 100%;
min-height: 100vh;
}

#invoice-POS h1,
#invoice-POS h2,
#invoice-POS h3,
#invoice-POS h4,
#invoice-POS h5,
#invoice-POS h6 {
color: #05070b;
font-weight: bolder;
}

#pos .pos-detail {
height: 42vh !important;
}

#pos .pos-detail .table-responsive {
max-height: 40vh !important;
height: 40vh !important;
border-bottom: none !important;
}

#pos .pos-detail .table-responsive tr {
font-size: 14px
}

#pos .card-order {
min-height: 100%;
}

#pos .card-order .main-header {
position: relative;
}

#pos .grandtotal {
text-align: center;
height: 40px;
background-color: #7ec8ca;
margin-bottom: 20px;
font-size: 1.2rem;
font-weight: 800;
padding: 5px;
}

#pos .list-grid .list-item .list-thumb img {
width: 100% !important;
height: 100px !important;
max-height: 100px !important;
object-fit: cover;
}

#pos .list-grid {
height: 100%;
min-height: 100%;
overflow: scroll;
}

#pos .brand-Active {
border: 2px solid;
}

.centred {
text-align: center;
align-content: center;
}



@media (min-width: 1024px) {
#pos .list-grid {
    height: 100vh;
    min-height: 100vh;
    overflow: scroll;
}

;

}

#pos .card.o-hidden {
width: 19%;
;
max-width: 19%;
;
min-width: 130px;
}

#pos .input-customer {
position: relative;
display: flex;
flex-wrap: unset;
align-items: stretch;
width: 100%;
}

#pos .card.o-hidden:hover {
cursor: pointer;
border: 1px solid;
}

* {
font-size: 14px;
line-height: 20px;
font-family: 'Ubuntu', sans-serif;
text-transform: capitalize;

}

td,
th,
tr,
table {
border-collapse: collapse;
}

tr {
border-bottom: 2px dotted #05070b;
}

table {
width: 100%;
}

tfoot tr th:first-child {
text-align: left;
}

.total {
font-weight: bold;
font-size: 12px;
}


.change {
font-size: 10px;
margin-top: 25px;
}

.centered {
text-align: center;
align-content: center;
}

#invoice-POS {
max-width: 400px;
margin: 0px auto;
}


#top .logo {
height: 100px;
width: 100px;
background-size: 100px 100px;
}

.info {
margin-bottom: 20px;
}

.info>h2 {
text-align: center;
font-size: 1.5rem;

}

.title {
float: right;
}

.title p {
text-align: right;
}

table {
width: 100%;
border-collapse: collapse;
}

#invoice-POS table tr {
border-bottom: 2px dotted #05070b;
}

.invoice_logo {
text-align: center;
}

.invoice_logo img {
text-align: center;
}

.tabletitle {
font-size: .5em;
background: #EEE;
}

#legalcopy {
margin-top: 5mm;
}

#legalcopy p {
text-align: center;
}

#bar {
text-align: center;
}

.quantity {
max-width: 95px;
width: 95px;
}

.quantity input {
text-align: center;
border: none;
}

.quantity .form-control:focus {
color: #374151;
background-color: unset;
border-color: #e1d5fd;
outline: 0;
box-shadow: unset;
}

.quantity span {
padding: 8px;
}
</style>

<div class="modal fade" id="printModal" tabindex="-1" aria-labelledby="printModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-body">

                    <div style="max-width: 400px; margin: 0 auto;" id="printHere">
                        <!-- Store Info -->
                        <div class="info">
                            <div class="invoice_logo text-center mb-2">
                                <img src="{{ asset('') }}admin/assets/images/khanjee_logo.png" class="pos-logo" alt="logo" width="120px" height="60px">
                            </div>
                            <p>
                                <span>Date: {{ now()->format('Y-m-d H:i:s') }}<br></span>
                                <span>Address: Shop # 1, Yousaf Plaza 3rd floor boltan market karachi<br></span>
                                {{-- <span>Email: admin@example.com<br></span> --}}
                                <span>Phone: 03128192613, 03432650990<br></span>
                                <span>Customer: <span class="customerName">walk-in-customer</span><br></span>
                                {{-- <span>Warehouse: Warehouse 1<br></span> --}}
                            </p>
                        </div>
                    
                        <!-- Items Table -->
                        <table class="table_data">
                            <tbody>
                                <tr>
                                    <td colspan="3">
                                        Avocat
                                        <br>
                                        <span>1.00 kg x 15.00</span>
                                    </td>
                                    <td style="text-align: right; vertical-align: bottom;">15.00</td>
                                </tr>
                    
                                <tr>
                                    <td colspan="3" class="total">Order Tax</td>
                                    <td class="total" style="text-align: right;">{{ env('CURRENCY_SYMBLE') }} <span class="order_tax">0.00</span></td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="total">Discount</td>
                                    <td class="total" style="text-align: right;">{{ env('CURRENCY_SYMBLE') }} <span class="discount">0.00</span></td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="total">Shipping</td>
                                    <td class="total" style="text-align: right;">{{ env('CURRENCY_SYMBLE') }}  <span class="shipping">0.00</span></td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="total">Grand Total</td>
                                    <td class="total" style="text-align: right;">{{ env('CURRENCY_SYMBLE') }} <span class="grand_total">00.00</span></td>
                                </tr>
                                <tr style="display: none;">
                                    <td colspan="3" class="total">Paid</td>
                                    <td class="total" style="text-align: right;">{{ env('CURRENCY_SYMBLE') }} <span class="paid">00.00</span></td>
                                </tr>
                                <tr style="display: none;">
                                    <td colspan="3" class="total">Due</td>
                                    <td class="total" style="text-align: right;">{{ env('CURRENCY_SYMBLE') }}  <span class="due">00.00</span></td>
                                </tr>
                            </tbody>
                        </table>
                    
                        <!-- Payment Info -->
                        <table class="change mt-3" style="font-size: 10px;">
                            <thead>
                                <tr style="background: #eee;">
                                    <th style="text-align: left;">Paid By:</th>
                                    <th colspan="2" style="text-align: center;">Amount:</th>
                                    <th style="text-align: right;">Change Return:</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="text-align: left;">Cash</td>
                                    <td colspan="2" style="text-align: center;" ><span class="amount_paid">00.00</span></td>
                                    <td style="text-align: right;"><span class="return_amount">0.00</span></td>
                                </tr>
                            </tbody>
                        </table>
                    
                        <!-- Footer -->
                        <div id="legalcopy" class="ml-2">
                            <p class="legal">
                                <strong>Thank You For Shopping With Us. Please Come Again</strong>
                            </p>
                    
                            <!-- Barcode -->
                            <div id="bar">
                                <div textmargin="0" fontoptions="bold" class="barcode">
                                    SL_1121
                                </div>
                            </div>
                        </div>
                    </div>
                    

                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-success w-100 py-2 fw-bold printNow">Print</button>
                </div>
            </div>
        </div>
    </div>


