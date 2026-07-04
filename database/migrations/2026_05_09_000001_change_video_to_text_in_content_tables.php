<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'abouts', 'documents', 'teams', 'partners',
        'laws', 'news', 'importants', 'diplomas', 'seminars',
        'consultings', 'usefuls', 'onlines', 'trainings',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $table): void {
                $table->text('video')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $table): void {
                $table->string('video')->nullable()->change();
            });
        }
    }
};
