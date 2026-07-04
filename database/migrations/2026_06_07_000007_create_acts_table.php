<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('acts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contractor_id')->constrained()->cascadeOnDelete();

            $table->string('number');
            $table->date('date');

            $table->string('status')->default('draft');

            // Суммы
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('nds_amount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            $table->text('comment')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acts');
    }
};
