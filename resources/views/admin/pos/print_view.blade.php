<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>POS Receipt</title>
    <link rel="stylesheet" href="pos_print.css">
    <style id="__web-inspector-hide-shortcut-style__">
        .__web-inspector-hide-shortcut__,
        .__web-inspector-hide-shortcut__ * {
            visibility: hidden !important;
        }
        @media print {
            img {
                display: block !important;
                visibility: visible !important;
            }
            }
    </style>
</head>

<body>
    <div style="max-width: 400px; margin: 0 auto;">
        <!-- Store Info -->
        <div class="info">
            <div class="invoice_logo text-center mb-2">
                <img src="{{ asset('') }}admin/assets/images/khanjee_logo.png" class="pos-logo" alt="logo" width="120px" height="60px">
            </div>
            <p>
                <span>Date: {{ now()->format('Y-m-d') }}<br></span>
                <span>Address: Shop # 1, Yousaf Plaza 3rd floor boltan market karachi<br></span>
                {{-- <span>Email: admin@example.com<br></span> --}}
                <span>Phone: 03128192613, 03432650990<br></span>
                <span>Customer: walk-in-customer<br></span>
                {{-- <span>Warehouse: Warehouse 1<br></span> --}}
            </p>
        </div>
        
        <!-- Items Table -->
        <table class="table_data" style="font-weight:600">
            <tbody>
                <tr>
                    <td colspan="3">
                        Avocat
                        <br>
                        <span>1.00 kg x 15.00</span>
                    </td>
                    <td style="text-align: right; vertical-align: bottom;">15.00</td>
                </tr>

                {{-- <tr>
                    <td colspan="3" class="total">Order Tax</td>
                    <td class="total" style="text-align: right;">USD 0.00 (0.00 %)</td>
                </tr> --}}
                <tr>
                    <td colspan="3" class="total">Discount</td>
                    <td class="total" style="text-align: right;">USD 0.00</td>
                </tr>
                <tr>
                    <td colspan="3" class="total">Shipping</td>
                    <td class="total" style="text-align: right;">USD 0.00</td>
                </tr>
                <tr>
                    <td colspan="3" class="total">Grand Total</td>
                    <td class="total" style="text-align: right;">USD 15.00</td>
                </tr>
                <tr style="display: none;">
                    <td colspan="3" class="total">Paid</td>
                    <td class="total" style="text-align: right;">USD 15.00</td>
                </tr>
                <tr style="display: none;">
                    <td colspan="3" class="total">Due</td>
                    <td class="total" style="text-align: right;">USD 0.00</td>
                </tr>
            </tbody>
        </table>

        <!-- Payment Info -->
        <table class="change mt-3" style="font-size: 10px;">
            <thead>
                <tr style="background: #eee;">
                    <th style="text-align: left; font-weight:600">Paid By:</th>
                    <th colspan="2" style="text-align: center; font-weight:600">Amount:</th>
                    <th style="text-align: right; font-weight:600">Change Return:</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: left; ">Cash</td>
                    <td colspan="2" style="text-align: center;">15.00</td>
                    <td style="text-align: right;">0.00</td>
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
                <div textmargin="0" fontoptions="bold" class="barcode" style="font-weight:800:height:30px;">
                    {{-- <svg class="vue-barcode-element" width="121px" height="62px" x="0px" y="0px" viewBox="0 0 121 62"
                        xmlns="http://www.w3.org/2000/svg" version="1.1">
                        <rect x="0" y="0" width="121" height="62" style="fill:#ffffff;"></rect>
                        <g transform="translate(10, 10)" style="fill:#000000;">
                            <rect x="0" y="0" width="2" height="25"></rect>
                            <rect x="3" y="0" width="1" height="25"></rect>
                            <rect x="6" y="0" width="1" height="25"></rect>
                            <rect x="11" y="0" width="2" height="25"></rect>
                            <rect x="14" y="0" width="3" height="25"></rect>
                            <rect x="18" y="0" width="1" height="25"></rect>
                            <rect x="22" y="0" width="1" height="25"></rect>
                            <rect x="26" y="0" width="2" height="25"></rect>
                            <rect x="29" y="0" width="3" height="25"></rect>
                            <rect x="33" y="0" width="1" height="25"></rect>
                            <rect x="35" y="0" width="1" height="25"></rect>
                            <rect x="38" y="0" width="2" height="25"></rect>
                            <rect x="44" y="0" width="1" height="25"></rect>
                            <rect x="46" y="0" width="3" height="25"></rect>
                            <rect x="50" y="0" width="4" height="25"></rect>
                            <rect x="55" y="0" width="2" height="25"></rect>
                            <rect x="60" y="0" width="1" height="25"></rect>
                            <rect x="63" y="0" width="1" height="25"></rect>
                            <rect x="66" y="0" width="2" height="25"></rect>
                            <rect x="69" y="0" width="3" height="25"></rect>
                            <rect x="74" y="0" width="1" height="25"></rect>
                            <rect x="77" y="0" width="1" height="25"></rect>
                            <rect x="80" y="0" width="1" height="25"></rect>
                            <rect x="83" y="0" width="4" height="25"></rect>
                            <rect x="88" y="0" width="2" height="25"></rect>
                            <rect x="93" y="0" width="3" height="25"></rect>
                            <rect x="97" y="0" width="1" height="25"></rect>
                            <rect x="99" y="0" width="2" height="25"></rect>
                            <text style="font: 15px monospace;" text-anchor="middle" x="50.5" y="42">
                                SL_1121
                            </text>
                        </g>
                    </svg> --}}
                    SL_1121
                </div>
            </div>
        </div>
    </div>
    <style>
       
    </style>
</body>

</html>
