<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(0)->after('is_primary');
        });

        // Инициализируем sort_order по текущему порядку (is_primary desc, created_at asc)
        $rows = DB::table('bank_accounts')->orderByDesc('is_primary')->orderBy('created_at')->get(['id']);
        foreach ($rows as $i => $row) {
            DB::table('bank_accounts')->where('id', $row->id)->update(['sort_order' => $i]);
        }
    }

    public function down(): void
    {
        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
