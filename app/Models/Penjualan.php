<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penjualan extends Model
{
    /** @use HasFactory<\Database\Factories\PenjualanFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tanggal_penjualan',
        'total_harga',
        'pelanggan_id',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_penjualan' => 'date',
        'total_harga' => 'decimal:2',
    ];

    /**
     * Get the pelanggan that owns the penjualan.
     */
    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class);
    }

    /**
     * Get all detail penjualan for the penjualan.
     */
    public function detailPenjualan(): HasMany
    {
        return $this->hasMany(DetailPenjualan::class);
    }
    
    /**
     * Get the user that owns the penjualan.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
