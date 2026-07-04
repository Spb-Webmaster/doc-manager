<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('acts', function (Blueprint $table) {
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete()->after('contractor_id');
        });
    }

    public function down(): void
    {
        Schema::table('acts', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Invoice::class);
        });
    }
};
