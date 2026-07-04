<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedSmallInteger('stamp_scale')->default(100)->after('stamp_path');
            $table->unsignedSmallInteger('signature_scale')->default(100)->after('signature_path');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['stamp_scale', 'signature_scale']);
        });
    }
};
