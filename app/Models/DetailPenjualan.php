<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPenjualan extends Model
{
    /** @use HasFactory<\Database\Factories\DetailPenjualanFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'penjualan_id',
        'produk_id',
        'jumlah_produk',
        'subtotal',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'jumlah_produk' => 'integer',
        'subtotal' => 'decimal:2',
    ];

    /**
     * Get the penjualan that owns the detail penjualan.
     */
    public function penjualan(): BelongsTo
    {
        return $this->belongsTo(Penjualan::class);
    }

    /**
     * Get the produk that owns the detail penjualan.
     */
    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class);
    }
}
