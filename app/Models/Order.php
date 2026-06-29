<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $table = 'orders';
    protected $fillable = [
        'order_no', 'outlet_id', 'table_id', 'customer_name', 
        'subtotal', 'tax_amount', 'service_amount', 'total_amount', 
        'payment_method', 'payment_status', 'order_status', 'ordered_at', 'completed_at'
    ];

    public function outlet(): BelongsTo {
        return $this->belongsTo(Outlet::class);
    }

    public function table(): BelongsTo {
        return $this->belongsTo(Table::class);
    }

    public function items(): HasMany {
        return $this->hasMany(OrderItem::class);
    }

    public function statusLogs(): HasMany {
        return $this->hasMany(OrderStatusLog::class);
    }
}