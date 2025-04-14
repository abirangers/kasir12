<?php

namespace App\Http\Controllers;

use App\Models\DetailPenjualan;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $produks = Produk::all();
        $pelanggans = Pelanggan::all();
        $cart = Session::get('cart', []);
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['subtotal'];
        }

        return Inertia::render('transaksi/Index', [
            'produks' => $produks,
            'pelanggans' => $pelanggans,
            'cart' => $cart,
            'total' => $total,
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
        $validated = $request->validate([
            'pelanggan_id' => 'nullable',
            'nama_pelanggan' => 'required_without:pelanggan_id',
            'alamat_pelanggan' => 'required_without:pelanggan_id',
            'no_hp_pelanggan' => 'required_without:pelanggan_id',
            'total_bayar' => 'required|numeric|min:0',
        ]);

        $cart = Session::get('cart', []);
        
        if (empty($cart)) {
            return redirect()->back()->with('error', 'Keranjang belanja kosong!');
        }

        // Hitung total harga dari keranjang
        $totalHarga = 0;
        foreach ($cart as $item) {
            $totalHarga += $item['subtotal'];
        }

        // Validasi total bayar
        if ($validated['total_bayar'] < $totalHarga) {
            return redirect()->back()->with('error', 'Jumlah pembayaran kurang dari total belanja!');
        }

        try {
            DB::beginTransaction();

            // Jika pelanggan baru, buat data pelanggan
            $pelangganId = $validated['pelanggan_id'];
            if (!$pelangganId) {
                $pelanggan = Pelanggan::create([
                    'nama' => $validated['nama_pelanggan'],
                    'alamat' => $validated['alamat_pelanggan'],
                    'no_hp' => $validated['no_hp_pelanggan'],
                ]);
                $pelangganId = $pelanggan->id;
            }

            // Buat penjualan
            $penjualan = Penjualan::create([
                'tanggal_penjualan' => now(),
                'total_harga' => $totalHarga,
                'pelanggan_id' => $pelangganId,
            ]);

            // Simpan detail penjualan dan kurangi stok
            foreach ($cart as $item) {
                DetailPenjualan::create([
                    'penjualan_id' => $penjualan->id,
                    'produk_id' => $item['id'],
                    'jumlah_produk' => $item['jumlah'],
                    'subtotal' => $item['subtotal'],
                ]);

                // Kurangi stok produk
                $produk = Produk::find($item['id']);
                $produk->stok -= $item['jumlah'];
                $produk->save();
            }

            // Hapus keranjang setelah transaksi berhasil
            Session::forget('cart');

            DB::commit();

            // Informasi untuk struk
            $kembalian = $validated['total_bayar'] - $totalHarga;
            $struk = [
                'penjualan_id' => $penjualan->id,
                'tanggal' => $penjualan->tanggal_penjualan,
                'pelanggan' => [
                    'id' => $pelangganId,
                    'nama' => $pelangganId ? Pelanggan::find($pelangganId)->nama : $validated['nama_pelanggan'],
                ],
                'items' => $cart,
                'total' => $totalHarga,
                'bayar' => $validated['total_bayar'],
                'kembalian' => $kembalian,
            ];

            return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil!')->with('struk', $struk);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menambahkan produk ke keranjang.
     */
    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'produk_id' => 'required|exists:produks,id',
            'jumlah' => 'required|integer|min:1',
        ]);

        $produkId = $validated['produk_id'];
        $jumlah = $validated['jumlah'];

        // Ambil data produk
        $produk = Produk::findOrFail($produkId);

        // Cek stok
        if ($produk->stok < $jumlah) {
            return redirect()->back()->with('error', 'Stok tidak mencukupi!');
        }

        // Ambil keranjang dari session
        $cart = Session::get('cart', []);

        // Cek apakah produk sudah ada di keranjang
        if (isset($cart[$produkId])) {
            // Jika sudah ada, cek apakah jumlah baru melebihi stok
            $newJumlah = $cart[$produkId]['jumlah'] + $jumlah;
            if ($produk->stok < $newJumlah) {
                return redirect()->back()->with('error', 'Stok tidak mencukupi!');
            }

            // Update jumlah dan subtotal
            $cart[$produkId]['jumlah'] = $newJumlah;
            $cart[$produkId]['subtotal'] = $newJumlah * $produk->harga;
        } else {
            // Jika belum ada, tambahkan produk ke keranjang
            $cart[$produkId] = [
                'id' => $produk->id,
                'nama' => $produk->nama,
                'harga' => $produk->harga,
                'jumlah' => $jumlah,
                'subtotal' => $jumlah * $produk->harga,
            ];
        }

        // Simpan keranjang ke session
        Session::put('cart', $cart);

        return redirect()->back()->with('success', 'Produk berhasil ditambahkan ke keranjang');
    }

    /**
     * Mengupdate jumlah produk di keranjang.
     */
    public function updateCart(Request $request)
    {
        $validated = $request->validate([
            'produk_id' => 'required|exists:produks,id',
            'jumlah' => 'required|integer|min:1',
        ]);

        $produkId = $validated['produk_id'];
        $jumlah = $validated['jumlah'];

        // Ambil data produk
        $produk = Produk::findOrFail($produkId);

        // Cek stok
        if ($produk->stok < $jumlah) {
            return redirect()->back()->with('error', 'Stok tidak mencukupi!');
        }

        // Ambil keranjang dari session
        $cart = Session::get('cart', []);

        // Update jumlah dan subtotal
        if (isset($cart[$produkId])) {
            $cart[$produkId]['jumlah'] = $jumlah;
            $cart[$produkId]['subtotal'] = $jumlah * $produk->harga;
        }

        // Simpan keranjang ke session
        Session::put('cart', $cart);

        return redirect()->back()->with('success', 'Keranjang berhasil diupdate');
    }

    /**
     * Menghapus produk dari keranjang.
     */
    public function removeFromCart(Request $request)
    {
        $validated = $request->validate([
            'produk_id' => 'required|exists:produks,id',
        ]);

        $produkId = $validated['produk_id'];

        // Ambil keranjang dari session
        $cart = Session::get('cart', []);

        // Hapus produk dari keranjang
        if (isset($cart[$produkId])) {
            unset($cart[$produkId]);
        }

        // Simpan keranjang ke session
        Session::put('cart', $cart);

        return redirect()->back()->with('success', 'Produk berhasil dihapus dari keranjang');
    }

    /**
     * Mengosongkan keranjang.
     */
    public function clearCart()
    {
        Session::forget('cart');

        return redirect()->back()->with('success', 'Keranjang berhasil dikosongkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
}
