<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('interviews', function (Blueprint $table) {
      $table->id();
      $table->foreignId('student_id')->constrained()->cascadeOnDelete();
      $table->foreignId('counselor_user_id')->constrained('users')->cascadeOnDelete();
      $table->foreignId('compliance_profile_id')
        ->constrained('student_compliance_profiles')
        ->cascadeOnDelete();

      $table->string('status')->default('draft');

      $table->longText('ai_question_prompt')->nullable();
      $table->longText('ai_question_raw')->nullable();

      $table->string('recording_path')->nullable();
      $table->string('recording_mime')->nullable();
      $table->unsignedBigInteger('recording_size')->nullable();

      $table->longText('transcript_text')->nullable();
      $table->json('ai_report_json')->nullable();
      $table->string('report_path')->nullable();

      $table->timestamp('started_at')->nullable();
      $table->timestamp('completed_at')->nullable();
      $table->timestamps();
    });
  }

  public function down(): void {
    Schema::dropIfExists('interviews');
  }
};
