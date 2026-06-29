<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kitchen_queues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained('outlets')->cascadeOnDelete();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('order_item_id')->constrained('order_items')->cascadeOnDelete();
            
            // Mengarah ke layar mana pesanan ini harus tampil?
            $table->foreignId('kitchen_station_id')->nullable()->constrained('kitchen_stations');
            
            // Status khusus antrean dapur
            $table->enum('status', ['WAITING', 'COOKING', 'READY'])->default('WAITING');
            
            // Pencatatan waktu SLA Dapur (Service Level Agreement)
            $table->timestamp('queued_at')->useCurrent(); // Kapan masuk antrean
            $table->timestamp('started_cooking_at')->nullable(); // Kapan koki klik "Cook"
            $table->timestamp('finished_at')->nullable(); // Kapan koki klik "Ready"
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kitchen_queues');
    }
};
