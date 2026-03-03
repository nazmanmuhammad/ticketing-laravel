<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `access_requests` MODIFY COLUMN `status` ENUM('draft', 'submitted', 'pending_approval', 'info_requested', 'approved', 'rejected', 'implemented') DEFAULT 'draft'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `access_requests` MODIFY COLUMN `status` ENUM('draft', 'submitted', 'pending_approval', 'approved', 'rejected', 'implemented') DEFAULT 'draft'");
    }
};
