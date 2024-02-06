<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('deck_helpers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('description')->nullable();
            $table->foreignId('main_card_id')->constrained('cards')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('card_deck_helper', function (Blueprint $table) {
            $table->foreignId('deck_helper_id')->constrained()->cascadeOnDelete();
            $table->foreignId('card_id')->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('card_deck_helper');
        Schema::dropIfExists('deck_helpers');
    }
};
