<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guide_apps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('phone', 20);
            $table->text('experience');
            $table->unsignedInteger('years_experience');
            $table->json('lang');
            $table->boolean('has_car')->nullable();
            $table->string('car_type')->nullable();
            $table->date('driving_license_expiry')->nullable();
            $table->string('car_number')->nullable();
            $table->string('country');
            $table->string('area');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejected_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guide_apps');
    }
};
