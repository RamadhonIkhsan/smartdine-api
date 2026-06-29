<?php

namespace App\Models;

use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Menu extends Model
{
    protected $table = 'menus';
    protected $fillable = ['company_id', 'category_id', 'name', 'description', 'image_url', 'price', 'cooking_time', 'stock', 'is_available'];

    protected static function booted(): void {
        static::addGlobalScope(new TenantScope);
    }

    public function category(): BelongsTo {
        return $this->belongsTo(Category::class);
    }
}