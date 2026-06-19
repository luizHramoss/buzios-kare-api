<?php

use App\Enums\AppointmentStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->foreignId('customer_id')
                ->constrained('customers')
                ->cascadeOnDelete();

            $table->string('service', 100);
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->text('notes')->nullable();

            $table->string('status', 20)->default(AppointmentStatus::AGENDADO->value);

            $table->decimal('value', 10, 2)->default(0);
            $table->string('payment_method', 30)->nullable();

            // Quem criou o agendamento (Customer ou Admin) — polimórfico manual
            $table->string('created_by_type', 20); // 'customer' | 'admin'
            $table->unsignedBigInteger('created_by_id');

            // Quem cancelou (se aplicável)
            $table->string('cancelled_by_type', 20)->nullable();
            $table->unsignedBigInteger('cancelled_by_id')->nullable();
            $table->text('cancellation_reason')->nullable();

            // Referência ao agendamento original em caso de remarcação
            $table->foreignId('rescheduled_from_id')
                ->nullable()
                ->constrained('appointments')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Índice crítico: usado em toda verificação de conflito/disponibilidade
            $table->index(['date', 'start_time', 'end_time', 'status'], 'idx_appointments_conflict_check');

            // Índice para "meus agendamentos" do cliente
            $table->index(['customer_id', 'status']);

            // Índice para dashboard (consultas por data)
            $table->index('date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
