<?php

use App\Enums\CustomerStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->string('name', 150);
            $table->string('cpf', 14)->unique();
            $table->date('birth_date');
            $table->string('email', 150)->unique();
            $table->string('phone', 20);
            $table->string('whatsapp', 20)->nullable();
            $table->text('notes')->nullable();
            $table->string('password');

            $table->string('status', 20)->default(CustomerStatus::ACTIVE->value);

            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();

            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('email');
            $table->index('cpf');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
