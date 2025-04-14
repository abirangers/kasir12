<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $produks = Produk::orderBy('id', 'desc')->get();
        
        return Inertia::render('admin/produk/Index', compact('produks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('admin/produk/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
        ]);

        Produk::create($validated);
        return redirect()->route('produks.index')->with('success', 'Produk berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Produk $produk)
    {
        // return Inertia::render('Produk/Show', compact('produk'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Produk $produk)
    {
        return Inertia::render('admin/produk/Edit', compact('produk'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Produk $produk)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
        ]);

        $produk->update($validated);
        return redirect()->route('produks.index')->with('success', 'Produk berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Produk $produk)
    {
        $produk->delete();
        return redirect()->route('produks.index')->with('success', 'Produk berhasil dihapus');
    }
}
