<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            // 5,2 artinya maksimal 3 digit angka utama dan 2 di belakang koma (misal: 100.00 atau 11.50)
            $table->decimal('tax_rate', 5, 2)->default(11.00)->after('email')->comment('Pajak dalam persen (%)');
            $table->decimal('service_rate', 5, 2)->default(0.00)->after('tax_rate')->comment('Service charge dalam persen (%)');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['tax_rate', 'service_rate']);
        });
    }
};