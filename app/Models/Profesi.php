<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Profesi extends Model
{
    protected $table = 'profesi';

    protected $fillable = [
        'nama_profesi',
        'kategori',
    ];

    public function dokter(): HasMany
    {
        return $this->hasMany(Dokter::class);
    }
}