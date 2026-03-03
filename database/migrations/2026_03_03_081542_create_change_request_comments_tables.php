<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('change_request_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('change_request_id')->constrained('change_requests')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->boolean('is_internal')->default(false);
            $table->foreignId('parent_id')->nullable()->constrained('change_request_comments')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('change_request_comment_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('comment_id');
            $table->foreign('comment_id', 'cr_comment_att_comment_fk')->references('id')->on('change_request_comments')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('file_name');
            $table->unsignedBigInteger('file_size')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('change_request_comment_attachments');
        Schema::dropIfExists('change_request_comments');
    }
};
