<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->integer('sorting')->default(1);
            $table->timestamps();
        });

        Schema::create('training_training_category', function (Blueprint $table): void {
            $table->foreignId('training_id')->constrained()->cascadeOnDelete();
            $table->foreignId('training_category_id')->constrained()->cascadeOnDelete();
            $table->primary(['training_id', 'training_category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_training_category');
        Schema::dropIfExists('training_categories');
    }
};
