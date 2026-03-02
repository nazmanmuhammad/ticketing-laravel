<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('access_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('system_id')->constrained('systems')->cascadeOnDelete();
            $table->enum('access_type', ['read', 'write', 'admin', 'custom'])->default('read');
            $table->string('custom_access_type')->nullable();
            $table->text('reason');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('status', ['draft', 'submitted', 'pending_approval', 'approved', 'rejected', 'implemented'])->default('draft');
            $table->integer('current_approval_level')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'requester_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('access_requests');
    }
};
