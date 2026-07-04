<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('self_employed', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Персональные данные
            $table->string('full_name');
            $table->string('inn', 12);

            // Адреса
            $table->string('register_address')->nullable();
            $table->string('address')->nullable();

            // Контакты
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();

            // Паспортные данные
            $table->string('passport_serial', 10)->nullable();
            $table->string('passport_number', 10)->nullable();
            $table->string('who_issued')->nullable();
            $table->date('date_issued')->nullable();

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
        Schema::dropIfExists('self_employed');
    }
};
