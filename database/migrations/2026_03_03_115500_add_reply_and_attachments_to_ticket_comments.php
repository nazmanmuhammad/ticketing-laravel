<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_comments', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->after('id')->constrained('ticket_comments')->nullOnDelete();
        });

        Schema::create('ticket_comment_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_comment_id')->constrained('ticket_comments')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_size')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_comment_attachments');

        Schema::table('ticket_comments', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });
    }
};
