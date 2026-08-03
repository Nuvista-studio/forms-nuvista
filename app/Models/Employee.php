<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'nik',
        'department',
        'business_unit',
        'site',
        'no_telepon',
        'email',
        'status',
    ];

    public const STATUS_ACTIVE = 'active';

    public const STATUS_RESIGNED = 'resigned';

    public function siteDetail(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site', 'id_site');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'employee_id');
    }

    public function assignedAssets(): HasMany
    {
        return $this->hasMany(Asset::class, 'assigned_employee_id');
    }

    public function pemeriksaan(): HasMany
    {
        return $this->hasMany(FormPemeriksaan::class, 'pengguna_employee_id');
    }

    public function perawatan(): HasMany
    {
        return $this->hasMany(FormPerawatan::class, 'pengguna_employee_id');
    }

    public function pengembalian(): HasMany
    {
        return $this->hasMany(FormPengembalian::class, 'pengguna_employee_id');
    }

    public function getSiteNameAttribute(): ?string
    {
        return $this->siteDetail?->site ?? $this->site;
    }
}
