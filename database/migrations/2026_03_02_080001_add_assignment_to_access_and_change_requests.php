<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('access_requests', function (Blueprint $table) {
            $table->foreignId('assigned_to')->nullable()->after('current_approval_level')->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_team_id')->nullable()->after('assigned_to')->constrained('teams')->nullOnDelete();
        });

        Schema::table('change_requests', function (Blueprint $table) {
            $table->foreignId('assigned_to')->nullable()->after('post_review_notes')->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_team_id')->nullable()->after('assigned_to')->constrained('teams')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('access_requests', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropForeign(['assigned_team_id']);
            $table->dropColumn(['assigned_to', 'assigned_team_id']);
        });

        Schema::table('change_requests', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropForeign(['assigned_team_id']);
            $table->dropColumn(['assigned_to', 'assigned_team_id']);
        });
    }
};
