<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Penjualan::with('pelanggan')
            ->orderBy('tanggal_penjualan', 'desc');

        // Tambahkan nama kasir (user) ke setiap penjualan
        $penjualan = $query->get()->map(function ($item) {
            $item->kasir = User::find($item->user_id)->name ?? 'Admin';
            return $item;
        });

        return Inertia::render('admin/laporan/Index', [
            'penjualan' => $penjualan,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $penjualan = Penjualan::with(['pelanggan', 'detailPenjualan.produk'])
            ->findOrFail($id);

        $penjualan->kasir = User::find($penjualan->user_id)->name ?? 'Admin';

        return Inertia::render('admin/laporan/Index', [
            'detail' => $penjualan,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Print the report.
     */
    public function print(Request $request)
    {
        $query = Penjualan::with(['pelanggan', 'user'])
            ->orderBy('tanggal_penjualan', 'desc');

        // Filter berdasarkan pencarian
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('pelanggan', function($q) use ($search) {
                      $q->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        // Filter berdasarkan rentang tanggal
        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('tanggal_penjualan', '>=', $request->from_date);
        }

        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('tanggal_penjualan', '<=', $request->to_date);
        }

        $penjualan = $query->get();

        // Tambahkan nama kasir (user) ke setiap penjualan
        foreach ($penjualan as $item) {
            $item->kasir = User::find($item->user_id)->name ?? 'Admin';
        }

        // Hitung total penjualan
        $totalPenjualan = $penjualan->sum('total_harga');

        $pdf = PDF::loadView('reports.penjualan', [
            'penjualan' => $penjualan,
            'totalPenjualan' => $totalPenjualan,
            'dari' => $request->from_date ?? 'Awal',
            'sampai' => $request->to_date ?? 'Sekarang',
        ]);

        return $pdf->stream('laporan-penjualan.pdf');
    }

    /**
     * Print detail report.
     */
    public function printDetail(string $id)
    {
        $penjualan = Penjualan::with(['pelanggan', 'detailPenjualan.produk', 'user'])
            ->findOrFail($id);

        $pdf = PDF::loadView('reports.detail-penjualan', [
            'penjualan' => $penjualan,
        ]);

        return $pdf->stream("detail-penjualan-{$id}.pdf");
    }
}
