<?php

namespace App\Services;

use DB;

class ProductStockService
{
    public function getStock($productId)
    {
        // Purchase (final)
        $purchase = DB::table('purchase_items as pi')
            ->join('purchases as p', 'pi.purchase_id', '=', 'p.id')
            ->where('pi.product_id', $productId)
            ->selectRaw("
                COALESCE(SUM(CASE WHEN p.document_type = 'P' THEN pi.quantity ELSE 0 END), 0)
                - COALESCE(SUM(CASE WHEN p.document_type = 'PR' THEN pi.quantity ELSE 0 END), 0) as total
            ")
            ->first();
        $purchaseQty = $purchase ? $purchase->total : 0;

        // Sale (final)
        $sale = DB::table('sale_details as sd')
            ->join('sale_summary as ss', 'sd.sale_summary_id', '=', 'ss.id')
            ->where('sd.product_id', $productId)
            ->selectRaw("
                COALESCE(SUM(CASE WHEN ss.document_type = 'S' THEN sd.quantity ELSE 0 END), 0)
                + COALESCE(SUM(CASE WHEN ss.document_type = 'PS' THEN sd.quantity ELSE 0 END), 0)
                - COALESCE(SUM(CASE WHEN ss.document_type = 'SR' THEN sd.quantity ELSE 0 END), 0) as total
            ")
            ->first();
        $saleQty = $sale ? $sale->total : 0;

        // Stock Adjustment
        $adjustment = DB::table('stock_adjustment_items as sai')
            ->join('stock_adjustments as sa', 'sai.adjustment_id', '=', 'sa.id')
            ->where('sai.product_id', $productId)
            ->selectRaw("
                COALESCE(SUM(CASE WHEN sai.adjustment_type = 'addition' THEN sai.quantity ELSE 0 END), 0)
                - COALESCE(SUM(CASE WHEN sai.adjustment_type = 'subtraction' THEN sai.quantity ELSE 0 END), 0) as total
            ")
            ->first();
        $adjustmentQty = $adjustment ? $adjustment->total : 0;

        // Final Stock
        $aaa = ($purchaseQty + $adjustmentQty) - ($saleQty);
       
        return $aaa;
    }
}
