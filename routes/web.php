<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ListAssetToolsController;
use App\Http\Controllers\ListSparePartController;
use App\Http\Controllers\ListSparePartMultipleController;
use App\Http\Controllers\LocationsController;
use App\Http\Controllers\StockAssetController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SuratpesananController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

// Login Register
Route::get('/', [AuthController::class, 'login'])->name('indexlogin');
Route::get('/registration', [AuthController::class, 'register'])->name('indexregister');
Route::get('/registration', [AuthController::class, 'register'])->name('indexregister');
Route::get('/registration/post', [AuthController::class, 'registerpost'])->name('indexregisterpost');

// Dashboard Page
Route::get('/dashboard', [DashboardController::class, 'index'])->name('indexdashboard');

// ATK
Route::get('/listatk', [DashboardController::class, 'listatk'])->name('indexatk');
Route::get('/atkin', [DashboardController::class, 'atkin'])->name('atkin');
Route::get('/atkout', [DashboardController::class, 'atkout'])->name('atkout');

// Supplier
Route::get('/supplier', [SupplierController::class, 'index'])->name('indexsupplier');
Route::get('/createsupplier', [SupplierController::class, 'create'])->name('createsupplier');
Route::post('/createsupplierpost', [SupplierController::class, 'store'])->name('createsupplierpost');
Route::delete('/deletesupplier/{id}', [SupplierController::class, 'destroy'])->name('deletesupplier');
Route::get('/editsupplier/{id}', [SupplierController::class, 'edit'])->name('editsupplier');
Route::put('/updatesupplier/{id}', [SupplierController::class, 'update'])->name('updatesupplier');

// Users
Route::get('/users', [UsersController::class, 'index'])->name('indexusers');
Route::get('/createusers', [UsersController::class, 'create'])->name('createusers');
Route::get('/editusers/{id}', [UsersController::class, 'edit'])->name('editusers');
Route::post('/createuserspost', [UsersController::class, 'store'])->name('createuserspost');
Route::put('/updateusers/{id}', [UsersController::class, 'update'])->name('updateuserspost');
Route::delete('/deleteusers/{id}', [UsersController::class, 'destroy'])->name('deleteusers');

// Brand
Route::get('/brand', [BrandController::class, 'index'])->name('indexbrand');
Route::get('/createbrand', [BrandController::class, 'create'])->name('createbrand');
Route::post('/createbrandpost', [BrandController::class, 'store'])->name('createbrandpost');
Route::delete('/deletebrand/{id}', [BrandController::class, 'destroy'])->name('deletebrand');
Route::get('/editbrand/{id}', [BrandController::class, 'edit'])->name('editbrand');
Route::put('/updatebrand/{id}', [BrandController::class, 'update'])->name('updatebrand');

// Warehouse
Route::get('/warehouse', [WarehouseController::class, 'index'])->name('indexwarehouse');
Route::get('/createwarehouse', [WarehouseController::class, 'create'])->name('createwarehouse');
Route::post('/createwarehousepost', [WarehouseController::class, 'store'])->name('createwarehousepost');
Route::delete('/deletewarehouse/{id}', [WarehouseController::class, 'destroy'])->name('deletewarehouse');
Route::get('/editwarehouse/{id}', [WarehouseController::class, 'edit'])->name('editwarehouse');
Route::put('/updatewarehouse/{id}', [WarehouseController::class, 'update'])->name('updatewarehouse');

// Lokasi
Route::get('/locations', [LocationsController::class, 'index'])->name('indexlocations');
Route::get('/create/locations', [LocationsController::class, 'create'])->name('createlocations');
Route::post('/create/locations/save', [LocationsController::class, 'store'])->name('storelocations');
Route::get('/edit/locations/{id}', [LocationsController::class, 'edit'])->name('editlocations');
Route::put('/update/locations/{id}', [LocationsController::class, 'update'])->name('updatelocations');
Route::delete('/delete/locations/{id}', [LocationsController::class, 'destroy'])->name('deletelocations');

// Category
Route::get('/category', [CategoryController::class, 'index'])->name('indexcategory');
Route::get('/create/category', [CategoryController::class, 'create'])->name('createcategory');
Route::post('/create/category/save', [CategoryController::class, 'store'])->name('storecategory');
Route::get('/edit/category/{id}', [CategoryController::class, 'edit'])->name('editcategory');
Route::put('/update/category/{id}', [CategoryController::class, 'update'])->name('updatecategory');
Route::delete('/delete/category/{id}', [CategoryController::class, 'destroy'])->name('deletecategory');

// Sub Category
Route::get('/subcategory', [SubCategoryController::class, 'index'])->name('indexsubcategory');
Route::get('/create/subcategory', [SubCategoryController::class, 'create'])->name('createsubcategory');
Route::post('/create/subcategory/save', [SubCategoryController::class, 'store'])->name('storesubcategory');
Route::get('/edit/subcategory/{id}', [SubCategoryController::class, 'edit'])->name('editsubcategory');
Route::put('/update/subcategory/{id}', [SubCategoryController::class, 'update'])->name('updatesubcategory');
Route::delete('/delete/subcategory/{id}', [SubCategoryController::class, 'destroy'])->name('deletesubcategory');

// routes/web.php
Route::get('/get-subcategories/{category_id}', [SubCategoryController::class, 'getByCategory'])->name('get.subcategories');

// Profile
Route::get('/profile', [DashboardController::class, 'profile'])->name('indexprofile');



// Login
Route::post('login_post', [AuthController::class, 'login_post'])->name('loginpost');

Route::get('/logout', [AuthController::class, 'logout'])->name('logout');;


Route::group(['middleware' => 'superadmin'], function () {
    
    Route::get('/dashboardsuperadmin', [DashboardController::class, 'index'])->name('indexdashboardsuperadmin');

});

Route::group(['middleware' => 'admin'], function () {
    Route::get('/dashboardadmin', [DashboardController::class, 'index'])->name('indexdashboardadmin');

});

Route::group(['middleware' => 'users'], function () {
    Route::get('/dashboardusers', [DashboardController::class, 'index'])->name('indexdashboarduser');

});

// Sparepart list In Out
Route::get('/sparepart', [ListSparePartController::class, 'index'])->name('spare-parts.index');
Route::get('/cardlistsparepart', [ListSparePartController::class, 'cardindex'])->name('card-list-spare-parts.index');

Route::get('/spare-parts/create', [ListSparePartController::class, 'create'])->name('spare-parts.create');
Route::post('/spare-parts', [ListSparePartController::class, 'store'])->name('spare-parts.store');
Route::get('/spare-parts/{id}/edit', [ListSparePartController::class, 'edit'])->name('spare-parts.edit');
Route::put('/spare-parts/{id}', [ListSparePartController::class, 'update'])->name('spare-parts.update');
Route::delete('/spare-parts/{id}', [ListSparePartController::class, 'destroy'])->name('spare-parts.destroy');
Route::get('/sparepart/pdf', [ListSparePartController::class, 'cetakPDF'])->name('sparepart.cetakpdf');
Route::get('/export-sparepart', [ListSparePartController::class, 'exportExcel'])->name('sparepart.export');

Route::get('/spare-parts/autocomplete', [ListSparePartController::class, 'autocomplete'])->name('spare-parts.autocomplete');

Route::get('/sparepart-in', [StockController::class, 'stockInIndex'])->name('stock-in.index');
Route::get('/sparepart-in/create', [StockController::class, 'stockInForm'])->name('stock-in.create');
Route::post('/sparepart-in', [StockController::class, 'storeStockIn'])->name('stock-in.store');
Route::get('export-stock-in', [StockController::class, 'exportStockInPDF'])->name('export.stock-in');

Route::get('/sparepart-out', [StockController::class, 'stockOutIndex'])->name('stock-out.index');
Route::get('/sparepart-out/create', [StockController::class, 'stockOutForm'])->name('stock-out.create');
Route::post('/sparepart-out', [StockController::class, 'storeStockOut'])->name('stock-out.store');
Route::get('export-stock-out', [StockController::class, 'exportStockOutPDF'])->name('export.stock-out');

Route::get('/spareparthistory', [StockController::class, 'history'])->name('sparepart.history');
Route::get('/spareparthistory/pdf', [StockController::class, 'exportHistoryPDF'])->name('sparepart.history.pdf');
Route::get('/sparepart/history/{id}', [StockController::class, 'viewHistoryPerItem'])->name('sparepartdetail.history');
Route::get('/sparepart/{id}/history/pdf', [StockController::class, 'exportHistoryPerItemPDF'])->name('sparepartdetail.history.pdf');
Route::get('/sparepart/{id}/export-excel', [StockController::class, 'exportHistoryPerItemExcel'])->name('sparepartdetail.history.excel');
Route::get('/export-history-excel', [StockController::class, 'exportHistoryExcel'])->name('sparepart.history.excel');

Route::get('/export-stock-in-excel', [StockController::class, 'exportStockInExcel'])->name('stockin.export.excel');
Route::get('/export-stock-out-excel', [StockController::class, 'exportStockOutExcel'])->name('stockout.export.excel');

// Asset Tools In Out
Route::get('/Assettools', [ListAssetToolsController::class, 'index'])->name('asset-tools.index');
Route::get('/cardlistAssettools', [ListAssetToolsController::class, 'cardindex'])->name('card-list-asset-tools.index');
Route::get('/asset-tools/create', [ListAssetToolsController::class, 'create'])->name('asset-tools.create');
Route::post('/asset-tools', [ListAssetToolsController::class, 'store'])->name('asset-tools.store');
Route::get('/asset-tools/{id}/edit', [ListAssetToolsController::class, 'edit'])->name('asset-tools.edit');
Route::put('/asset-tools/{id}', [ListAssetToolsController::class, 'update'])->name('asset-tools.update');
Route::delete('/asset-tools/{id}', [ListAssetToolsController::class, 'destroy'])->name('asset-tools.destroy');
Route::get('/assettool/pdf', [ListAssetToolsController::class, 'cetakPDF'])->name('assettools.cetakpdf');
Route::get('/export-assettools', [ListAssetToolsController::class, 'exportExcel'])->name('assettools.export');

Route::get('/asset-in', [StockAssetController::class, 'stockInIndex'])->name('asset-in.index');
Route::get('/asset-in/create', [StockAssetController::class, 'stockInForm'])->name('asset-in.create');
Route::post('/asset-in', [StockAssetController::class, 'storeStockIn'])->name('asset-in.store');
Route::get('export-asset-stock-in', [StockAssetController::class, 'exportStockInPDF'])->name('export.asset-stock-in');

Route::get('/asset-out', [StockAssetController::class, 'stockOutIndex'])->name('asset-out.index');
Route::get('/asset-out/create', [StockAssetController::class, 'stockOutForm'])->name('asset-out.create');
Route::post('/asset-out', [StockAssetController::class, 'storeStockOut'])->name('asset-out.store');
Route::get('export-asset-stock-out', [StockAssetController::class, 'exportStockOutPDF'])->name('export.asset-stock-out');

Route::get('/assettoolshistory', [StockAssetController::class, 'history'])->name('assettools.history');
Route::get('/assettoolhistory/pdf', [StockAssetController::class, 'exportHistoryPDF'])->name('assettools.history.pdf');
Route::get('/assettool/history/{id}', [StockAssetController::class, 'viewHistoryPerItem'])->name('assettoolsdetail.history');
Route::get('/assettool/{id}/history/pdf', [StockAssetController::class, 'exportHistoryPerItemPDF'])->name('assettoolsdetail.history.pdf');
Route::get('/assettools/{id}/export-excel', [StockAssetController::class, 'exportHistoryPerItemExcel'])->name('assettoolsdetail.history.excel');
Route::get('/assettools-export-history-excel', [StockAssetController::class, 'exportHistoryExcel'])->name('assettools.history.excel');

Route::get('/export-asset-stock-in-excel', [StockAssetController::class, 'exportStockInExcel'])->name('assetstockin.export.excel');
Route::get('/export-asset-stock-out-excel', [StockAssetController::class, 'exportStockOutExcel'])->name('assetstockout.export.excel');

// Spare Part In Multiple
Route::get('/sparepart/in/multiple', [ListSparePartMultipleController::class, 'index'])->name('sparepartinmultiple.index');
Route::get('/listsparepart/in/multiple/create', [ListSparePartMultipleController::class, 'create'])->name('sparepartinmultiple.create');
Route::post('/listsparepart/in/multiple/post', [ListSparePartMultipleController::class, 'storein'])->name('sparepartinmultiple.store');
Route::get('/stockinmultiple/{id}', [ListSparePartMultipleController::class, 'show'])->name('sparepartinmultiple.show');

// Spare Part Out Multiple
Route::get('/sparepart/out/multiple', [ListSparePartMultipleController::class, 'indexout'])->name('sparepartoutmultiple.index');
Route::get('/listsparepart/out/multiple/create', [ListSparePartMultipleController::class, 'createout'])->name('sparepartoutmultiple.createout');
Route::post('/listsparepart/out/multiple/post', [ListSparePartMultipleController::class, 'storeout'])->name('sparepartoutmultiple.store');
Route::get('/stockoutmultiple/{id}', [ListSparePartMultipleController::class, 'showout'])->name('sparepartoutmultiple.show');

Route::get('/spareparts/search', [ListSparePartMultipleController::class, 'search']);
Route::post('/spare-parts/import', [ListSparePartController::class, 'import'])->name('spare-parts.import');


Route::get('/suratpesanan', [SuratpesananController::class, 'index'])->name('suratpesanan.index');
Route::get('/create/suratpesanan', [SuratpesananController::class, 'create'])->name('suratpesanan.create');
Route::post('/create/suratpesanan/post', [SuratpesananController::class, 'store'])->name('suratpesanan.store');
Route::get('/create/suratpesanan/{id}', [SuratpesananController::class, 'edit'])->name('suratpesanan.edit');
Route::put('/create/suratpesanan/{id}', [SuratpesananController::class, 'update'])->name('suratpesanan.update');
Route::delete('/delete/suratpesanan/{id}', [SuratpesananController::class, 'destroy'])->name('suratpesanan.delete');
Route::get('/show/suratpesanan/{id}', [SuratpesananController::class, 'show'])->name('suratpesanan.show');

Route::get('suratpesanan/{id}/pdf', [SuratpesananController::class, 'printPdf'])->name('suratpesanan.pdf');
Route::get('/spareparts/{id}/stock', [SuratpesananController::class, 'getStock']);

Route::prefix('suratpesanan')->name('suratpesanan.')->group(function () {
    Route::post('{id}/submit', [SuratPesananController::class, 'submit'])->name('submit');
    Route::post('{id}/approve', [SuratPesananController::class, 'approve'])->name('approve');
    Route::post('{id}/reject', [SuratPesananController::class, 'reject'])->name('reject');
});