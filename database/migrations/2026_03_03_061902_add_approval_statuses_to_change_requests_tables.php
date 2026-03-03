<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE `change_requests` MODIFY COLUMN `status` ENUM('draft', 'submitted', 'under_review', 'pending_approval', 'info_requested', 'rejected', 'approved', 'scheduled', 'implemented', 'closed', 'failed') DEFAULT 'draft'");
        DB::statement("ALTER TABLE `change_request_approvals` MODIFY COLUMN `status` ENUM('pending', 'approved', 'rejected', 'info_requested', 'rescheduled') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `change_requests` MODIFY COLUMN `status` ENUM('draft', 'submitted', 'under_review', 'approved', 'scheduled', 'implemented', 'closed', 'failed') DEFAULT 'draft'");
        DB::statement("ALTER TABLE `change_request_approvals` MODIFY COLUMN `status` ENUM('pending', 'approved', 'rejected', 'rescheduled') DEFAULT 'pending'");
    }
};
