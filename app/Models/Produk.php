<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produk extends Model
{
    /** @use HasFactory<\Database\Factories\ProdukFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
        'harga',
        'stok',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'harga' => 'decimal:2',
        'stok' => 'integer',
    ];

    /**
     * Get all detail penjualan for the produk.
     */
    public function detailPenjualan(): HasMany
    {
        return $this->hasMany(DetailPenjualan::class);
    }
}
