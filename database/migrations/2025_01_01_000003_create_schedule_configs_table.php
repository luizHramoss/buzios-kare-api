<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedule_configs', function (Blueprint $table) {
            $table->id();

            $table->time('work_start')->default('08:00:00');
            $table->time('work_end')->default('18:00:00');
            $table->time('break_start')->nullable();
            $table->time('break_end')->nullable();

            $table->unsignedSmallInteger('slot_duration')->default(60);
            $table->unsignedSmallInteger('min_advance_hours')->default(2);
            $table->unsignedSmallInteger('max_advance_days')->default(60);
            $table->unsignedTinyInteger('max_future_appointments')->default(3);
            $table->unsignedSmallInteger('cancellation_advance_hours')->default(24);

            $table->json('allowed_days');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_configs');
    }
};
