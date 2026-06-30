<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'company_id',
        'email',
        'fullname',
        'username', // Menggunakan username sesuai perubahan sebelumnya
        'password',
        'role_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Relasi ke Company (User ini bekerja di perusahaan mana)
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}