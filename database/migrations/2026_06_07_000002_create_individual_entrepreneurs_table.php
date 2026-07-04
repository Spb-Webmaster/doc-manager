<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('individual_entrepreneurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Наименование
            $table->string('name')->nullable();
            $table->string('full_name');

            // Идентификация
            $table->string('inn', 12);
            $table->string('ogrnip', 15)->nullable();
            $table->string('okved')->nullable();

            // Адреса
            $table->string('register_address')->nullable();
            $table->string('address')->nullable();

            // Контакты
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();

            // Налогообложение
            $table->boolean('payment_nds')->default(false);
            $table->unsignedBigInteger('taxation_id')->nullable();

            // Банковские реквизиты
            $table->string('bank')->nullable();
            $table->string('payment_account', 20)->nullable();
            $table->string('bik', 9)->nullable();
            $table->string('correspondent_account', 20)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('individual_entrepreneurs');
    }
};
