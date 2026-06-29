<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiningTable extends Model
{
    protected $table = 'dining_tables';
    protected $fillable = ['outlet_id', 'table_no', 'qr_code', 'is_active'];

    public function outlet(): BelongsTo {
        return $this->belongsTo(Outlet::class);
    }
}