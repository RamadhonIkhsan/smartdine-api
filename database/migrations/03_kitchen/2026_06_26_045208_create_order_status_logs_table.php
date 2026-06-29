<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_status_logs', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke transaksi utama
            $table->foreignId('order_id')
                  ->constrained('orders')
                  ->cascadeOnDelete();
                  
            // Status baru dari pesanan tersebut
            $table->enum('status', [
                'PENDING', 
                'WAITING_PAYMENT', 
                'PAID', 
                'COOKING', 
                'READY', 
                'COMPLETED', 
                'CANCELLED',
                'REFUNDED'
            ]);
            
            // Siapa yang mengubah status? (Bisa Kasir, Kitchen, atau Owner)
            // Nullable karena bisa jadi diubah otomatis oleh sistem (misal: Midtrans Webhook) atau Customer
            $table->foreignId('changed_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete(); 
                  
            // Catatan opsional: Sangat berguna untuk alasan Cancel / Refund
            $table->text('notes')->nullable();
            
            // Laravel otomatis membuat created_at (waktu status berubah) dan updated_at
            $table->timestamps(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_logs');
    }
};
