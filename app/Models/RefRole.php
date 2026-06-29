<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RefRole extends Model
{
    use HasApiTokens;
    
    protected $table = 'ref_role';
    protected $fillable = ['id', 'role_name', 'description', 'is_active', 'seq'];
}