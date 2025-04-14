<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class PelangganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pelanggans = Pelanggan::orderBy('nama', 'asc')->get();
        
        return Inertia::render('admin/pelanggan/Index', [
            'pelanggans' => $pelanggans
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('admin/pelanggan/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'no_hp' => 'required|string|max:15',
        ]);

        Pelanggan::create($validated);

        return redirect()->route('pelanggans.index')
            ->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pelanggan $pelanggan)
    {
        return Inertia::render('admin/pelanggan/Show', [
            'pelanggan' => $pelanggan
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pelanggan $pelanggan)
    {
        return Inertia::render('admin/pelanggan/Edit', [
            'pelanggan' => $pelanggan
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pelanggan $pelanggan)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'no_hp' => 'required|string|max:15',
        ]);

        $pelanggan->update($validated);

        return redirect()->route('pelanggans.index')
            ->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pelanggan $pelanggan)
    {
        // Cek jika pelanggan masih memiliki transaksi
        if ($pelanggan->penjualan()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus pelanggan yang memiliki riwayat transaksi.');
        }

        $pelanggan->delete();

        return redirect()->route('pelanggans.index')
            ->with('success', 'Pelanggan berhasil dihapus.');
    }
}
