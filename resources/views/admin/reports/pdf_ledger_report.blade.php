<!DOCTYPE html>
<html>
<head>
    <title>Customer Ledger Report</title>
    <style>
        body { 
            font-family: DejaVu Sans, sans-serif; 
            font-size: 11px; 
            margin: 0;
            padding: 0;
        }

        .company-header { 
            text-align: center; 
            margin-bottom: 5px; 
        }
        .company-header h2 { 
            margin: 0; 
            font-size: 18px; 
            font-weight: bold; 
        }
        .company-header p { 
            margin: 2px 0; 
            line-height: 1.4; 
        }

        .customer-info { 
            margin-top: 10px; 
            margin-bottom: 10px; 
        }
        .customer-info table td { 
            padding: 2px 0; 
            border: none !important;
        }

        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px; 
        }
        th, td { 
            border: 1px solid #000; 
            padding: 5px; 
        }
        th { 
            background: #f1f1f1; 
            font-weight: bold; 
        }
        td { 
            font-size: 11px; 
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .summary-total td { 
            font-weight: bold; 
            background: #fafafa; 
        }

        @page {
            margin-top: 15mm;
            margin-left: 12mm;
            margin-right: 12mm;
            margin-bottom: 20mm; /* space for footer */
        }
    </style>
</head>
<body>

    <div class="company-header">
        <h2>Khanjee Beauty Mart</h2>
        <p>
            Ph: 0312-8192613, 0343-2650990 <br>
            Branch-1: Shop #1, Yousaf Plaza, 3rd Floor, Bolton Market, Karachi <br>
            Branch-2: RJ Mall, Shop # LG-15, Karachi <br>
            Branch-3: Iqbal Market, Shop #39, Bolton Market, Karachi
        </p>
        <hr>
        <h3 style="margin-top: 5px;">Customer Ledger Report</h3>
    </div>

    <div class="customer-info">
        <table width="100%">
            <tr>
                <td width="50%"><strong>Customer Name:</strong> {{ $customer->name ?? 'N/A' }}</td>
                <td width="50%"><strong>Phone:</strong> {{ $customer->phone ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Address:</strong> {{ $customer->address ?? 'N/A' }}</td>
                <td><strong>Date Range:</strong> {{ $from ?? '---' }} to {{ $to ?? '---' }}</td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
        <tr>
            <th width="4%">#</th>
            <th width="14%">Date</th>
            <th width="12%">Ref</th>
            <th>Description</th>
            <th width="12%" class="text-right">Debit</th>
            <th width="12%" class="text-right">Credit</th>
            <th width="12%" class="text-right">Balance</th>
        </tr>
        </thead>

        <tbody>
        @php 
            $i = 1;
            $totalDebit = 0;
            $totalCredit = 0;
        @endphp
        
        @if(count($ledgerData) > 0)
            @foreach($ledgerData as $row)
            @php
                $totalDebit += $row['debit'] ?? 0;
                $totalCredit += $row['credit'] ?? 0;
            @endphp
            <tr>
                <td class="text-center">{{ $i++ }}</td>
                <td>{{ $row['date'] ?? '---' }}</td>
                <td>
                    @if(isset($row['sale_id']) && $row['sale_id'])
                        {{ $row['reference'] ?? '---' }}
                    @else
                        {{ $row['payment_type'] ?? '---' }}
                    @endif
                </td>
                <td>{{ $row['description'] ?? '---' }}</td>
                <td class="text-right">{{ number_format($row['debit'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($row['credit'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($row['balance'] ?? 0, 2) }}</td>
            </tr>
            @endforeach
        @else
            <tr>
                <td colspan="7" class="text-center">No transactions found for the selected period</td>
            </tr>
        @endif
        </tbody>

        @php
            $finalBalance = $totalDebit - $totalCredit;
        @endphp

        <tfoot>
        <tr class="summary-total">
            <td colspan="4" class="text-right"><strong>Total:</strong></td>
            <td class="text-right"><strong>{{ number_format($totalDebit, 2) }}</strong></td>
            <td class="text-right"><strong>{{ number_format($totalCredit, 2) }}</strong></td>
            <td class="text-right"><strong>{{ number_format($finalBalance, 2) }}</strong></td>
        </tr>
        </tfoot>
    </table>


{{-- <!-- ✅ Correct Footer Script -->
<script type="text/php">
if (isset($pdf)) {
    $font = $fontMetrics->getFont("DejaVu Sans", "normal");
    $size = 9;

    // Printed Date - bottom-left
    $pdf->page_text(30, $pdf->get_height() - 40, "Printed: {{ date('d-M-Y H:i') }}", $font, $size);

    // Page number - bottom-right
    $pdf->page_text($pdf->get_width() - 120, $pdf->get_height() - 40, "Page {PAGE_NUM} of {PAGE_COUNT}", $font, $size);
}
</script> --}}

</body>
</html>
