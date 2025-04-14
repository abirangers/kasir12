<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pelanggan extends Model
{
    /** @use HasFactory<\Database\Factories\PelangganFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
        'alamat',
        'no_hp',
    ];

    /**
     * Get all penjualan for the pelanggan.
     */
    public function penjualan(): HasMany
    {
        return $this->hasMany(Penjualan::class);
    }
}
