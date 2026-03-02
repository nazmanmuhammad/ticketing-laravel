<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('change_request_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('change_request_id')->constrained('change_requests')->cascadeOnDelete();
            $table->foreignId('approver_id')->constrained('users')->cascadeOnDelete();
            $table->integer('level');
            $table->enum('status', ['pending', 'approved', 'rejected', 'rescheduled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('acted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('change_request_approvals');
    }
};
