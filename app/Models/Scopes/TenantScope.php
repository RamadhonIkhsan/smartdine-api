<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        // Jika user sudah login dan perannya bukan SUPERADMIN (SaaS Admin)
        if (Auth::hasUser() && Auth::user()->role !== 'SUPERADMIN') {
            // Otomatis filter berdasarkan company_id milik user yang sedang login
            $builder->where($model->getTable() . '.company_id', Auth::user()->company_id);
        }
    }
}