<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DetailPesananController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\KurirController;
use App\Http\Controllers\LanggananController;
use App\Http\Controllers\LaporanPenjualanController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PengirimanController;
use App\Http\Controllers\ProdukAirController;
use App\Http\Controllers\RiwayatStockController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});
