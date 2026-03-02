<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('change_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->enum('change_type', ['standard', 'normal', 'emergency'])->default('normal');
            $table->foreignId('system_id')->constrained('systems')->cascadeOnDelete();
            $table->text('impact')->nullable();
            $table->text('risk')->nullable();
            $table->text('rollback_plan')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('implemented_at')->nullable();
            $table->enum('status', ['draft', 'submitted', 'under_review', 'approved', 'scheduled', 'implemented', 'closed', 'failed'])->default('draft');
            $table->text('post_review_notes')->nullable();
            $table->foreignId('related_ticket_id')->nullable()->constrained('tickets')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'change_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('change_requests');
    }
};
