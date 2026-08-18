<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperatorSubmenuAkses extends Model
{
    protected $table = 'operator_submenu_akses';

    protected $fillable = [
        'user_id',
        'submenu',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}