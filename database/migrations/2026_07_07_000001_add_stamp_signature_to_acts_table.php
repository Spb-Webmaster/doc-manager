<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('acts', function (Blueprint $table) {
            $table->string('stamp_path')->nullable()->after('pdf_path');
            $table->unsignedSmallInteger('stamp_scale')->default(100)->after('stamp_path');
            $table->string('signature_path')->nullable()->after('stamp_scale');
            $table->unsignedSmallInteger('signature_scale')->default(100)->after('signature_path');
        });
    }

    public function down(): void
    {
        Schema::table('acts', function (Blueprint $table) {
            $table->dropColumn(['stamp_path', 'stamp_scale', 'signature_path', 'signature_scale']);
        });
    }
};
