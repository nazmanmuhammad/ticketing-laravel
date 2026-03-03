<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('access_request_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('access_request_id')->constrained('access_requests')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->boolean('is_internal')->default(false);
            $table->foreignId('parent_id')->nullable()->constrained('access_request_comments')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('access_request_comment_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('comment_id');
            $table->foreign('comment_id', 'ar_comment_att_comment_fk')->references('id')->on('access_request_comments')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('file_name');
            $table->unsignedBigInteger('file_size')->nullable();
            $table->timestamps();
        });

        Schema::create('access_request_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('access_request_id')->constrained('access_requests')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->text('description');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('access_request_comment_attachments');
        Schema::dropIfExists('access_request_comments');
        Schema::dropIfExists('access_request_activities');
    }
};
