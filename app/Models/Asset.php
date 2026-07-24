<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'kategori',
        'brand',
        'tipe',
        'nama_perangkat',
        'no_serial',
        'no_asset',
        'qr_code',
        'status',
    ];

    public function pemeriksaan(): HasMany
    {
        return $this->hasMany(FormPemeriksaan::class);
    }

    public function perawatan(): HasMany
    {
        return $this->hasMany(FormPerawatan::class);
    }
}
