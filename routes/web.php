<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('auth.login');
// });

require __DIR__ . '/auth.php';

Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'verified']], function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboardInfo', [App\Http\Controllers\Admin\DashboardController::class, 'dashboardInfo'])->name('dashboardInfo');
    Route::get('change_password', [App\Http\Controllers\Admin\UserController::class, 'setting_view'])->name('change_password');
    Route::post('passwordChange', [App\Http\Controllers\Admin\UserController::class, 'passwordChange'])->name('passwordChange');
    Route::resource('/user', App\Http\Controllers\Admin\UserController::class);
    Route::post('/user_delete', [App\Http\Controllers\Admin\UserController::class, 'user_delete'])->name('user_delete');
    Route::post('/user_status', [App\Http\Controllers\Admin\UserController::class, 'user_status'])->name('user_status');

    Route::get('/module', [App\Http\Controllers\Admin\ModuleController::class, 'index'])->name('module.index');
    Route::post('/module_store', [App\Http\Controllers\Admin\ModuleController::class, 'store'])->name('module.store');
    Route::get('/module/{id}', [App\Http\Controllers\Admin\ModuleController::class, 'edit'])->name('module.edit');
    Route::put('/module/{id}', [App\Http\Controllers\Admin\ModuleController::class, 'update'])->name('module.update');
    Route::delete('/module/{id}', [App\Http\Controllers\Admin\ModuleController::class, 'destroy'])->name('module.destroy');

    Route::resource('/role', App\Http\Controllers\Admin\RoleController::class);
    Route::post('/role_filter', [App\Http\Controllers\Admin\RoleController::class, 'filter'])->name('rolefilter');
    Route::post('/role_delete', [App\Http\Controllers\Admin\RoleController::class, 'role_delete'])->name('role_delete');

    Route::resource('/general_settings', App\Http\Controllers\Admin\GeneralSettingController::class);

    Route::resource('/category', App\Http\Controllers\Admin\CategoryController::class);
    Route::post('/category/deleteAll', [App\Http\Controllers\Admin\CategoryController::class, 'deleteAll'])->name('deleteAll');
    Route::post('/category/rec_update', [App\Http\Controllers\Admin\CategoryController::class, 'rec_update'])->name('rec_update');
    Route::get('/loadCategories', [App\Http\Controllers\Admin\CategoryController::class, 'loadCategories'])->name('loadCategories');

    Route::resource('/brand', App\Http\Controllers\Admin\BrandController::class);
    Route::post('/brand/deleteAll', [App\Http\Controllers\Admin\BrandController::class, 'deleteAll'])->name('deleteAll');
    Route::post('/brand/rec_update', [App\Http\Controllers\Admin\BrandController::class, 'rec_update'])->name('rec_update');
    Route::get('/loadBrands', [App\Http\Controllers\Admin\BrandController::class, 'loadBrands'])->name('loadBrands');

    Route::resource('/unit', App\Http\Controllers\Admin\UnitController::class);
    Route::post('/unit/deleteAll', [App\Http\Controllers\Admin\UnitController::class, 'deleteAll'])->name('deleteAll');
    Route::post('/unit/rec_update', [App\Http\Controllers\Admin\UnitController::class, 'rec_update'])->name('rec_update');
    Route::get('/loadUnits', [App\Http\Controllers\Admin\UnitController::class, 'loadUnits'])->name('loadUnits');

    Route::resource('/product', App\Http\Controllers\Admin\ProductController::class);
    Route::post('/product/deleteAll', [App\Http\Controllers\Admin\ProductController::class, 'deleteAll'])->name('deleteAll');
    Route::post('/product/rec_update', [App\Http\Controllers\Admin\ProductController::class, 'rec_update'])->name('rec_update');
    Route::get('/product_search', [App\Http\Controllers\Admin\ProductController::class, 'product_search'])->name('product_search');
    Route::get('/product_search_for_sale', [App\Http\Controllers\Admin\ProductController::class, 'product_search_for_sale'])->name('product_search_for_sale');
    Route::get('/product/view/{id}', [App\Http\Controllers\Admin\ProductController::class, 'product_view'])->name('product_view');

    Route::resource('/supplier', App\Http\Controllers\Admin\SupplierController::class);
    Route::post('/supplier/deleteAll', [App\Http\Controllers\Admin\SupplierController::class, 'deleteAll'])->name('deleteAll');
    Route::post('/supplier/rec_update', [App\Http\Controllers\Admin\SupplierController::class, 'rec_update'])->name('rec_update');
    Route::get('/loadSuppliers', [App\Http\Controllers\Admin\SupplierController::class, 'loadSuppliers'])->name('loadSuppliers');

    Route::resource('/warehouse', App\Http\Controllers\Admin\WarehouseController::class);
    Route::post('/warehouse/deleteAll', [App\Http\Controllers\Admin\WarehouseController::class, 'deleteAll'])->name('deleteAll');
    Route::post('/warehouse/rec_update', [App\Http\Controllers\Admin\WarehouseController::class, 'rec_update'])->name('rec_update');

    Route::resource('/customer', App\Http\Controllers\Admin\CustomerController::class);
    Route::post('/customer/deleteAll', [App\Http\Controllers\Admin\CustomerController::class, 'deleteAll'])->name('deleteAll');
    Route::post('/customer/rec_update', [App\Http\Controllers\Admin\CustomerController::class, 'rec_update'])->name('rec_update');
    Route::get('/loadCustomers', [App\Http\Controllers\Admin\CustomerController::class, 'loadCustomers'])->name('loadCustomers');


    Route::resource('/purchase', App\Http\Controllers\Admin\PurchaseController::class);
    Route::post('/purchase/StorePurchase', [App\Http\Controllers\Admin\PurchaseController::class, 'StorePurchase'])->name('StorePurchase');
    Route::get('getPurchaseView/{id?}', [App\Http\Controllers\Admin\PurchaseController::class, 'getPurchaseView'])->name('getPurchaseView');
    Route::post('/purchase/rec_update', [App\Http\Controllers\Admin\PurchaseController::class, 'rec_update'])->name('rec_update');
    Route::post('/purchase/storeFinalPurchase', [App\Http\Controllers\Admin\PurchaseController::class, 'storeFinalPurchase'])->name('storeFinalPurchase');
    Route::get('/getAverageCostAndSalePrice/{id}', [App\Http\Controllers\Admin\PurchaseController::class, 'getAverageCostAndSalePrice'])->name('getAverageCostAndSalePrice');
    Route::get('/getProductStock/{id}', [App\Http\Controllers\Admin\PurchaseController::class, 'getProductStock'])->name('getProductStock');
    Route::post('/purchase/deleteAll', [App\Http\Controllers\Admin\PurchaseController::class, 'deleteAll'])->name('deleteAll');
    Route::post('/purchase/pdelete/{id}', [App\Http\Controllers\Admin\PurchaseController::class, 'pdelete'])->name('pdelete');
    Route::get('/purchase/purchaseEdit/{id}', [App\Http\Controllers\Admin\PurchaseController::class, 'purchaseEdit'])->name('purchaseEdit');
    Route::post('/purchase/storeFinalPurchaseEdit', [App\Http\Controllers\Admin\PurchaseController::class, 'storeFinalPurchaseEdit'])->name('storeFinalPurchaseEdit');
    Route::post('/purchase/pTempDelete/{id}', [App\Http\Controllers\Admin\PurchaseController::class, 'pTempDelete'])->name('pTempDelete');
    Route::get('/purchase/getLastPurchases/{id}', [App\Http\Controllers\Admin\PurchaseController::class, 'getLastPurchases'])->name('getLastPurchases');

    Route::get('/purchase/view/detail/{id}', [App\Http\Controllers\Admin\PurchaseController::class, 'purchase_view'])->name('purchase_view');
    Route::get('/purchase/{id}/download', [App\Http\Controllers\Admin\PurchaseController::class, 'purchase_download'])->name('purchase.download');


    Route::resource('/purchase_return', App\Http\Controllers\Admin\PurchaseReturnController::class);
    Route::get('/purchase_return/purchaseReturn/{id}', [App\Http\Controllers\Admin\PurchaseReturnController::class, 'purchaseReturn'])->name('purchaseReturn');
    ;
    Route::get('/purchaseReturnItems/{id}', [App\Http\Controllers\Admin\PurchaseReturnController::class, 'purchaseReturnItems'])->name('purchaseReturnItems');
    Route::post('/purchase_return/purchaseReturnStore', [App\Http\Controllers\Admin\PurchaseReturnController::class, 'purchaseReturnStore'])->name('purchaseReturnStore');
    Route::get('/purchase_return/view/detail/{id}', [App\Http\Controllers\Admin\PurchaseReturnController::class, 'viewDetail'])->name('stock_adjustment.view.detail');



    Route::resource('/sale', App\Http\Controllers\Admin\SaleController::class);
    Route::post('/sale/StoreSale', [App\Http\Controllers\Admin\SaleController::class, 'StoreSale'])->name('StoreSale');
    Route::delete('/pos_destroy/{id}', [App\Http\Controllers\Admin\SaleController::class, 'pos_destroy'])->name('pos_destroy');
    Route::post('/sale/pos/posStoreSale', [App\Http\Controllers\Admin\SaleController::class, 'posStoreSale'])->name('posStoreSale');
    Route::get('getSaleView/{id?}', [App\Http\Controllers\Admin\SaleController::class, 'getSaleView'])->name('getSaleView');
    Route::get('pos_getSaleView/{id?}', [App\Http\Controllers\Admin\SaleController::class, 'pos_getSaleView'])->name('pos_getSaleView');
    Route::post('/sale/rec_update', [App\Http\Controllers\Admin\SaleController::class, 'rec_update'])->name('rec_update');
    Route::post('/sale/storeFinalSale', [App\Http\Controllers\Admin\SaleController::class, 'storeFinalSale'])->name('storeFinalSale');
    Route::post('/sale/storeFinalSaleDraft', [App\Http\Controllers\Admin\SaleController::class, 'posStoreFinalSaleDraft'])->name('posStoreFinalSaleDraft');
    Route::post('/sale/deleteAll', [App\Http\Controllers\Admin\SaleController::class, 'deleteAll'])->name('deleteAll');
    Route::post('/sale/posDeleteAll', [App\Http\Controllers\Admin\SaleController::class, 'posDeleteAll'])->name('posDeleteAll');
    Route::post('/sale/saleDelete/{id}', [App\Http\Controllers\Admin\SaleController::class, 'saleDelete'])->name('saleDelete');
    Route::get('/sale/saleEdit/{id}', [App\Http\Controllers\Admin\SaleController::class, 'saleEdit'])->name('saleEdit');
    Route::post('/sale/storeFinalSaleEdit', [App\Http\Controllers\Admin\SaleController::class, 'storeFinalSaleEdit'])->name('storeFinalPurchaseEdit');
    Route::post('/sale/UpdateSaleItem', [App\Http\Controllers\Admin\SaleController::class, 'UpdateSaleItem'])->name('UpdateSaleItem');
    Route::post('posUpdateSaleItem', [App\Http\Controllers\Admin\SaleController::class, 'posUpdateSaleItem'])->name('posUpdateSaleItem');
    Route::post('/sale/saleTempDelete/{id}', [App\Http\Controllers\Admin\SaleController::class, 'saleTempDelete'])->name('saleTempDelete');
    // Last 3 Sales by Product (and optional customer)
    Route::get('/sale/lastSale/{product_id}/{customer_id?}', [App\Http\Controllers\Admin\SaleController::class, 'getLastSales'])->name('getLastSales');

    Route::get('/sale/view/detail/{id}', [App\Http\Controllers\Admin\SaleController::class, 'sale_view'])->name('sale_view');
    Route::get('/sale/{id}/download', [App\Http\Controllers\Admin\SaleController::class, 'sale_download'])->name('sale.download');
    Route::get('/sale/{id}/sale_with_profit_download', [App\Http\Controllers\Admin\SaleController::class, 'sale_with_profit_download'])->name('sale_with_profit.download');




    Route::resource('/sale_return', App\Http\Controllers\Admin\SaleReturnController::class);
    Route::get('/sale_return/saleReturn/{id}', [App\Http\Controllers\Admin\SaleReturnController::class, 'saleReturn'])->name('saleReturn');
    ;
    Route::get('/saleReturnItems/{id}', [App\Http\Controllers\Admin\SaleReturnController::class, 'saleReturnItems'])->name('saleReturnItems');
    Route::post('/sale_return/saleReturnStore', [App\Http\Controllers\Admin\SaleReturnController::class, 'saleReturnStore'])->name('saleReturnStore');
    Route::get('/sale_return/view/detail/{id}', [App\Http\Controllers\Admin\SaleReturnController::class, 'viewDetail'])->name('sale_return.view.detail');

    Route::resource('/stock_adjustment', App\Http\Controllers\Admin\StockAdjustmentController::class);
    Route::post('/stock_adjustment/StoreStockAdjustment', [App\Http\Controllers\Admin\StockAdjustmentController::class, 'StoreStockAdjustment'])->name('StoreStockAdjustment');
    Route::get('getStockAdjustmentView/{id?}', [App\Http\Controllers\Admin\StockAdjustmentController::class, 'getStockAdjustmentView'])->name('getStockAdjustmentView');
    Route::post('/stock_adjustment/rec_update', [App\Http\Controllers\Admin\StockAdjustmentController::class, 'rec_update'])->name('rec_update');
    Route::post('/stock_adjustment/storeFinalStockAdjustment', [App\Http\Controllers\Admin\StockAdjustmentController::class, 'storeFinalStockAdjustment'])->name('storeFinalStockAdjustment');
    Route::post('/stock_adjustment/deleteAll', [App\Http\Controllers\Admin\StockAdjustmentController::class, 'deleteAll'])->name('deleteAll');
    Route::post('/stock_adjustment/StockAdjustmentDelete/{id}', [App\Http\Controllers\Admin\StockAdjustmentController::class, 'StockAdjustmentDelete'])->name('StockAdjustmentDelete');
    Route::get('/stock_adjustment/StockAdjustmentEdit/{id}', [App\Http\Controllers\Admin\StockAdjustmentController::class, 'StockAdjustmentEdit'])->name('StockAdjustmentEdit');
    Route::post('/stock_adjustment/storeFinalStockAdjustmentEdit', [App\Http\Controllers\Admin\StockAdjustmentController::class, 'storeFinalStockAdjustmentEdit'])->name('storeFinalStockAdjustmentEdit');
    Route::post('/stock_adjustment/UpdateStockAdjustmentItem', [App\Http\Controllers\Admin\StockAdjustmentController::class, 'UpdateStockAdjustmentItem'])->name('UpdateStockAdjustmentItem');
    Route::post('/stock_adjustment/StockAdjustmentTempDelete/{id}', [App\Http\Controllers\Admin\StockAdjustmentController::class, 'StockAdjustmentTempDelete'])->name('StockAdjustmentTempDelete');
    Route::get('/stock_adjustment/view/detail/{id}', [App\Http\Controllers\Admin\StockAdjustmentController::class, 'viewDetail'])->name('stock_adjustment.view.detail');


    Route::resource('/expense_category', App\Http\Controllers\Admin\ExpenseCategoryController::class);
    Route::get('/loadExpenseCategory', [App\Http\Controllers\Admin\ExpenseCategoryController::class, 'loadExpenseCategory'])->name('loadExpenseCategory');
    Route::post('/expense_category/rec_update', [App\Http\Controllers\Admin\ExpenseCategoryController::class, 'rec_update'])->name('rec_update');
    Route::resource('/expense', App\Http\Controllers\Admin\ExpenseController::class);
    Route::post('/expense/rec_update', [App\Http\Controllers\Admin\ExpenseController::class, 'rec_update'])->name('rec_update');



    //product report
    Route::get('/reports/product_report', [App\Http\Controllers\Admin\ReportsController::class, 'product_report'])->name('reports.product_report');
    Route::get('/reports/data', [App\Http\Controllers\Admin\ReportsController::class, 'getData'])->name('getData');
    Route::get('/reports/product/{id}/details', [App\Http\Controllers\Admin\ReportsController::class, 'getProductDetails'])->name('reports.product.details');
    // Product Report PDF Routes
    Route::get('/reports/products/pdf', [App\Http\Controllers\Admin\ReportsController::class, 'downloadSummaryPdf'])->name('reports.products.pdf');
    Route::get('/reports/product_detail/{id}/pdf', [App\Http\Controllers\Admin\ReportsController::class, 'downloadDetailPdf'])->name('reports.product.details.pdf');

    //expense report
    Route::get('/reports/expense', [App\Http\Controllers\Admin\ReportsController::class, 'expenseReport'])
        ->name('reports.expenseReport');

    Route::get('/reports/expense/data', [App\Http\Controllers\Admin\ReportsController::class, 'getExpenseData'])
        ->name('reports.expense.data');
    //expense report close

    //product pourchase report
    Route::get('/reports/purchase_report', [App\Http\Controllers\Admin\ReportsController::class, 'purchaseReport'])
        ->name('reports.purchaseReport');

    Route::get('/reports/purchase_report/data', [App\Http\Controllers\Admin\ReportsController::class, 'getPurchaseReportData'])
        ->name('reports.purchase_report.data');
    //product pourchase report close


    //product sale report
    Route::get('/reports/sale_report', [App\Http\Controllers\Admin\ReportsController::class, 'saleReport'])
        ->name('reports.saleReport');

    Route::get('/reports/sale_report/data', [App\Http\Controllers\Admin\ReportsController::class, 'getSaleReportData'])
        ->name('reports.sale_report.data');

    //porduct sale report close


    //customer ledger report
    Route::get('/reports/customer-ledger', [App\Http\Controllers\Admin\ReportsController::class, 'customerLedgerReport'])->name('reports.customerLedgerReport');
    Route::get('/reports/customer-ledger/data', [App\Http\Controllers\Admin\ReportsController::class, 'getCustomerLedgerData'])->name('reports.customer.ledger.data');
    //customer ledger report close


    //stock report
    Route::get('/reports/stock-report', [App\Http\Controllers\Admin\ReportsController::class, 'stockReport'])->name('reports.stockReport');
    Route::get('/reports/stock-report/data', [App\Http\Controllers\Admin\ReportsController::class, 'getStockReportData'])->name('stock.report.data');
    Route::get('/reports/stock-report/{id}', [App\Http\Controllers\Admin\ReportsController::class, 'stockReportDetail'])->name('stock.report.detail');
    // Detail Page
//    Route::get('admin/reports/stock-report/{id}', [ReportController::class, 'stockReportDetail'])->name('stock.report.detail');

    // Ajax for each tab
    Route::get('admin/reports/stock-report/{id}/purchase', [App\Http\Controllers\Admin\ReportsController::class, 'getStockPurchase'])->name('stock.report.purchase');
    Route::get('admin/reports/stock-report/{id}/purchase-return', [App\Http\Controllers\Admin\ReportsController::class, 'getStockPurchaseReturn'])->name('stock.report.purchase.return');
    Route::get('admin/reports/stock-report/{id}/sale', [App\Http\Controllers\Admin\ReportsController::class, 'getStockSale'])->name('stock.report.sale');
    Route::get('admin/reports/stock-report/{id}/sale-return', [App\Http\Controllers\Admin\ReportsController::class, 'getStockSaleReturn'])->name('stock.report.sale.return');
    Route::get('admin/reports/stock-report/{id}/adjustment', [App\Http\Controllers\Admin\ReportsController::class, 'getStockAdjustment'])->name('stock.report.adjustment');

    //stock report close


    //POS
    Route::get('/pos', [App\Http\Controllers\Admin\PosController::class, 'index'])->name('pos.index');
    Route::get('/print_view', [App\Http\Controllers\Admin\PosController::class, 'print_view'])->name('pos.print_view');


});