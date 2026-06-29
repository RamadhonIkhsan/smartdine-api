<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $table = 'companies';
    protected $fillable = ['name', 'domain', 'phone', 'email', 'tax_rate', 'service_rate', 'is_active'];

    public function outlets(): HasMany {
        return $this->hasMany(Outlet::class);
    }

    public function users(): HasMany {
        return $this->hasMany(User::class);
    }

    public function categories(): HasMany {
        return $this->hasMany(Category::class);
    }
}