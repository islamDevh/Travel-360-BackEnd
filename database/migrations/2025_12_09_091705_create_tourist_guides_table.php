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
        Schema::create('tourist_guides', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('profile_image')->nullable();
            $table->text('experiences')->nullable();
            $table->foreignId('language_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('years_of_experience')->nullable();
            $table->string('driving_license_image')->nullable();
            $table->date('license_expiry_date')->nullable();
            $table->string('cv')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tourist_guides');
    }
};
