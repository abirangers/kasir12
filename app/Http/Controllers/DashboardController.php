<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Models\Produk;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard admin
     */
    public function index()
    {
        // Hitung jumlah produk
        $productCount = Produk::count();
        
        // Hitung jumlah pelanggan
        $customerCount = Pelanggan::count();
        
        // Hitung jumlah transaksi
        $transactionCount = Penjualan::count();
        
        // Hitung total penjualan
        $totalSales = Penjualan::sum('total_harga');
        
        return Inertia::render('Dashboard', [
            'stats' => [
                'productCount' => $productCount,
                'customerCount' => $customerCount,
                'transactionCount' => $transactionCount,
                'totalSales' => $totalSales
            ]
        ]);
    }
} 