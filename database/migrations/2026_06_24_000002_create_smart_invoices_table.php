<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('smart_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_template_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('period_months');  // 1, 2, 3, 6
            $table->unsignedTinyInteger('day_of_month');   // 1–31
            $table->boolean('with_act')->default(false);
            $table->boolean('is_active')->default(true);
            $table->date('next_run_at')->nullable();
            $table->timestamp('last_run_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('smart_invoices');
    }
};
