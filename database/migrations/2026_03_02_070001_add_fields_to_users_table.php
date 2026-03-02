<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('department')->nullable()->after('email');
            $table->string('phone')->nullable()->after('department');
            $table->foreignId('team_id')->nullable()->after('phone')->constrained('teams')->nullOnDelete();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->dropColumn(['department', 'phone', 'team_id']);
            $table->dropSoftDeletes();
        });
    }
};
