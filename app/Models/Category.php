<?php

namespace App\Models;

use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $table = 'categories';
    protected $fillable = ['company_id', 'name', 'icon', 'sort_order'];

    /**
     * Relasi ke Company (Kategori ini milik perusahaan apa)
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relasi ke Menu (Nanti digunakan untuk mengecek apakah kategori aman dihapus)
     */
    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }
}