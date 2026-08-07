<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bed extends Model
{
    protected $table = 'bed';

    protected $fillable = [
        'kamar_id',
        'nomor_bed',
        'status',
    ];

    public function kamar(): BelongsTo
    {
        return $this->belongsTo(Kamar::class);
    }

    public function rawatInap(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(RawatInap::class);
    }
}