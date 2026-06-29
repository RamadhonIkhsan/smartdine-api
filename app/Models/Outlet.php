<?php

namespace App\Models;

use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\DiningTable;


class Outlet extends Model
{
    protected $table = 'outlets';
    protected $fillable = ['company_id', 'name', 'address', 'phone', 'is_active'];

    // Pasang TenantScope agar outlet yang tampil hanya milik company user
    protected static function booted(): void {
        static::addGlobalScope(new TenantScope);
    }

    public function company(): BelongsTo {
        return $this->belongsTo(Company::class);
    }

    public function diningTables(): HasMany {
        return $this->hasMany(DiningTable::class, 'outlet_id');
    }
}