<?php

namespace App\Models;

use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $table = 'categories';
    protected $fillable = ['company_id', 'name', 'icon', 'sort_order'];

    protected static function booted(): void {
        static::addGlobalScope(new TenantScope);
    }

    public function menus(): HasMany {
        return $this->hasMany(Menu::class);
    }
}