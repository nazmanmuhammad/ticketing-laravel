<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->string('title');
            $table->text('description');
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('sub_category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['open', 'in_progress', 'pending', 'resolved', 'closed'])->default('open');
            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_team_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->timestamp('sla_due_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'priority']);
            $table->index('requester_id');
            $table->index('assigned_to');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
