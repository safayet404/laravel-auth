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
        Schema::table('interview_questions', function (Blueprint $table) {
            $table->string('recording_path')->nullable();
            $table->string('recording_mime')->nullable();
            $table->unsignedBigInteger('recording_size')->nullable();
            $table->string('status')->default('pending');

            $table->longText('transcript_text')->nullable();

            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interview_questions', function (Blueprint $table) {
            //
        });
    }
};
