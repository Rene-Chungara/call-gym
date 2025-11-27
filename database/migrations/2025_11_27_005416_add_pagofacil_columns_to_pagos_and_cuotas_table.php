<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->string('pagofacil_transaction_id')->nullable()->after('stripe_status');
            $table->text('qr_image')->nullable()->after('pagofacil_transaction_id'); // Base64 del QR
        });

        Schema::table('cuotas_pago', function (Blueprint $table) {
            $table->string('pagofacil_transaction_id')->nullable()->after('estado');
            $table->text('qr_image')->nullable()->after('pagofacil_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropColumn(['pagofacil_transaction_id', 'qr_image']);
        });

        Schema::table('cuotas_pago', function (Blueprint $table) {
            $table->dropColumn(['pagofacil_transaction_id', 'qr_image']);
        });
    }
};
