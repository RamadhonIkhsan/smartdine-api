<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasApiTokens;
    
    protected $table = 'users';
    protected $fillable = ['company_id', 'name', 'email', 'password', 'role'];
    protected $hidden = ['password', 'remember_token'];

    public function company(): BelongsTo {
        return $this->belongsTo(Company::class);
    }
}