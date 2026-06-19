<?php

use App\Enums\BlockType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blocked_schedules', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->date('date');
            $table->time('start_time')->nullable(); // null = dia inteiro
            $table->time('end_time')->nullable();
            $table->string('type', 20)->default(BlockType::FULL_DAY->value);
            $table->string('reason')->nullable();

            $table->foreignId('created_by_id')
                ->constrained('admins')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->index(['date', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blocked_schedules');
    }
};
