<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('access_request_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('access_request_id')->constrained('access_requests')->cascadeOnDelete();
            $table->foreignId('approver_id')->constrained('users')->cascadeOnDelete();
            $table->integer('level');
            $table->enum('status', ['pending', 'approved', 'rejected', 'info_requested'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('acted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('access_request_approvals');
    }
};
