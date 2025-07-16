<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\PostalCodeController;
use App\Http\Controllers\OngkirMengantarController;
use App\Http\Controllers\UploadShopeeController;
use App\Http\Controllers\UploadMengantarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\EditorController;
use App\Http\Controllers\KampanyeController;
use App\Http\Controllers\IklanController;
use App\Http\Middleware\RoleMiddleware;

Route::get('/', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $role = Auth::user()->role;

    if ($role === 'admin') {
        return redirect()->route('dashboard.admin');
    } elseif ($role === 'customerservice') {
        return redirect()->route('dashboard.customerservice');
    }

    // Jika role tidak dikenali
    abort(403, 'Unauthorized');
});

// Form login dan proses
Route::get('/login', [UserController::class, 'showLoginForm'])->name('login');
Route::post('/login', [UserController::class, 'login'])->name('login.process');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');



Route::middleware(['auth',RoleMiddleware::class . ':admin',])->group(function () {
// Dashboard
Route::get('/dashboard/admin', [DashboardController::class, 'indexadmin'])->name('dashboard.admin');
//kelolaakun
Route::get('/users/create', [UserController::class, 'create'])->name('user.create');
Route::post('/users/store', [UserController::class, 'store'])->name('user.store');
//kelola cs
Route::get('/user/customerservice', [UserController::class, 'indexCS'])->name('user.cs');
Route::get('/user/{id}/edit-voucher', [UserController::class, 'editVoucher'])->name('user.editVoucher');
Route::post('/user/{id}/update-voucher', [UserController::class, 'updateVoucher'])->name('user.updateVoucher');
Route::delete('/usercs/{id}', [UserController::class, 'destroy'])->name('user.destroy');
//kelola editor
Route::get('/user/editor', [UserController::class, 'indexEd'])->name('user.editor');
Route::delete('/user/{id}', [UserController::class, 'destroyEd'])->name('user.destroyEd');
// Kelola Produk
Route::get('/produk', [ProdukController::class, 'index'])->name('produk.index');
Route::get('/produk/create', [ProdukController::class, 'create'])->name('produk.create');
Route::post('/produk', [ProdukController::class, 'store'])->name('produk.store');
Route::get('/produk/{produk}/edit', [ProdukController::class, 'edit'])->name('produk.edit');
Route::put('/produk/{produk}', [ProdukController::class, 'update'])->name('produk.update');
Route::delete('/produk/{produk}', [ProdukController::class, 'destroy'])->name('produk.destroy');
//kelolapenjualan
Route::get('/penjualanlaporan', [PenjualanController::class, 'laporan'])->name('penjualan.laporan');
//kelolalead
Route::get('/lead-laporan', [LeadController::class, 'laporan'])->name('lead.laporan');
Route::get('/penjualan/resiexcel', [PenjualanController::class, 'resiExcel'])->name('penjualan.resiexcel');
//kelola job editor
Route::get('/editor/laporan', [EditorController::class, 'laporan'])->name('editor.laporan');
//kelola kode iklan kampanye
Route::get('/kampanye', [KampanyeController::class, 'index'])->name('kampanye.index');
Route::get('/kampanye/create', [KampanyeController::class, 'create'])->name('kampanye.create');
Route::post('/kampanye', [KampanyeController::class, 'store'])->name('kampanye.store');
Route::get('/kampanye/{id}/edit', [KampanyeController::class, 'edit'])->name('kampanye.edit');
Route::put('/kampanye/{id}', [KampanyeController::class, 'update'])->name('kampanye.update');
Route::delete('/kampanye/{id}', [KampanyeController::class, 'destroy'])->name('kampanye.destroy');
//import iklan
Route::get('/iklan/import', [IklanController::class, 'showImportForm'])->name('iklan.import.form');
Route::post('/iklan/import', [IklanController::class, 'import'])->name('iklan.import');
});




Route::middleware(['auth',RoleMiddleware::class . ':customerservice',])->group(function () {
Route::get('/dashboard/customerservice', [DashboardController::class, 'indexcustomerservice'])->name('dashboard.customerservice')->middleware('auth');

//kelolapenjualan
Route::get('/penjualanindividu', [PenjualanController::class, 'index'])->name('penjualan.index');
Route::get('/penjualan/create', [PenjualanController::class, 'create'])->name('penjualan.create');
Route::post('/penjualan/store', [PenjualanController::class, 'store'])->name('penjualan.store');
//kelolaleadpribadi
 Route::get('/lead', [LeadController::class, 'index'])->name('lead.index');
 Route::get('/lead/create', [LeadController::class, 'create'])->name('lead.create');
    Route::post('/lead/store', [LeadController::class, 'store'])->name('lead.store');
Route::get('/lead/{id}/edit', [LeadController::class, 'edit'])->name('lead.edit');
    Route::put('/lead/{id}', [LeadController::class, 'update'])->name('lead.update');
    Route::delete('/lead/{id}', [LeadController::class, 'destroy'])->name('lead.destroy');
//kelola shopee
Route::get('/penjualan/shopee', [PenjualanController::class, 'cekShopee'])->name('penjualan.cekshopee');
Route::post('/penjualan/ambil/{id}', [PenjualanController::class, 'ambilPenjualan'])->name('penjualan.ambil');


});

Route::middleware(['auth',RoleMiddleware::class . ':editor',])->group(function () {
Route::get('/dashboard/editor', [DashboardController::class, 'indexeditor'])->name('dashboard.editor');
//job
Route::get('/editor/jobdesk', [EditorController::class, 'jobdesk'])->name('editor.jobdesk');
Route::post('/editor/jobdesk/ambil', [EditorController::class, 'ambilJobdesk'])->name('editor.jobdesk.ambil');
Route::get('/editor/jobdesk/index', [EditorController::class, 'index'])->name('editor.jobdesk.index');
Route::post('/editor/jobdesk/selesai', [EditorController::class, 'selesai'])->name('editor.jobdesk.selesai');
Route::get('/editor/jobdesk/done', [EditorController::class, 'done'])->name('editor.jobdesk.done');
Route::delete('/editor/jobdesk/{id}', [EditorController::class, 'hapus'])->name('editor.jobdesk.hapus');

});


Route::middleware('auth')->group(function () {
//profil    
   Route::get('/profil', [ProfilController::class, 'index'])->name('profil.index');
Route::get('/profil/edit', [ProfilController::class, 'edit'])->name('profil.edit');
Route::post('/profil/update', [ProfilController::class, 'update'])->name('profil.update');
//penjualan
Route::get('/penjualan/{id}/edit', [PenjualanController::class, 'edit'])->name('penjualan.edit');
Route::put('/penjualan/{id}', [PenjualanController::class, 'update'])->name('penjualan.update');
Route::delete('/penjualanindv/{id}', [PenjualanController::class, 'destroy'])->name('penjualanindv.destroy');
Route::get('/penjualan/{id}/detail', [PenjualanController::class, 'show'])->name('penjualan.detail');
//export
Route::get('/export-penjualan', [PenjualanController::class, 'exportExcel'])->name('penjualan.export');
});



Route::get('/proxy/mengantar-address', [OngkirMengantarController::class, 'searchAddress']);
Route::get('/proxy/mengantar-estimate', [OngkirMengantarController::class, 'getEstimate']);
















Route::get('/get-regencies', [PostalCodeController::class, 'getRegencies']);
Route::get('/get-districts', [PostalCodeController::class, 'getDistricts']);
Route::get('/get-villages', [PostalCodeController::class, 'getVillages']);
Route::get('/get-postal-code', function (\Illuminate\Http\Request $request) {
    $data = \App\Models\IndonesiaPostalCode::where('province', $request->province)
        ->where('regency', $request->regency)
        ->where('district', $request->district)
        ->where('village', $request->village)
        ->first();

    return response()->json([
        'postal_code' => $data ? $data->postal_code : null
    ]);
});


Route::get('/upload-shopee', [UploadShopeeController::class, 'form']);
Route::post('/upload-shopee', [UploadShopeeController::class, 'import'])->name('upload.shopee');

Route::post('/upload/mengantar', [UploadMengantarController::class, 'import'])->name('upload.mengantar');
