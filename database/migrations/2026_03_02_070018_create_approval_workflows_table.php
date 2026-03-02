<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_workflows', function (Blueprint $table) {
            $table->id();
            $table->string('module');
            $table->foreignId('system_id')->nullable()->constrained('systems')->nullOnDelete();
            $table->integer('level');
            $table->foreignId('approver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('approver_role')->nullable();
            $table->integer('sla_hours')->default(24);
            $table->timestamps();

            $table->unique(['module', 'system_id', 'level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_workflows');
    }
};
