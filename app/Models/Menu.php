<?php

namespace App\Models;

use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Menu extends Model
{
    protected $table = 'menus';
    protected $fillable = ['company_id', 'category_id', 'name', 'description', 'image_url', 'price', 'cooking_time', 'stock', 'is_available'];

    /**
     * Relasi ke Category (Menu ini masuk kategori apa)
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relasi ke Company (Menu ini milik perusahaan apa)
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}