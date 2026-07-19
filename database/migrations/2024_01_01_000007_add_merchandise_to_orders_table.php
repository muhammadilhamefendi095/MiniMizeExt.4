<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Karya (artwork_id) sekarang boleh kosong, karena order bisa juga untuk merchandise
            $table->foreignId('artwork_id')->nullable()->change();
            $table->foreignId('merchandise_id')->nullable()->after('artwork_id')
                ->constrained('merchandises')->cascadeOnDelete();
            $table->integer('quantity')->default(1)->after('merchandise_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('merchandise_id');
            $table->dropColumn('quantity');
            $table->foreignId('artwork_id')->nullable(false)->change();
        });
    }
};
