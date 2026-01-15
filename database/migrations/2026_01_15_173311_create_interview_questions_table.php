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
        Schema::create('interview_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interview_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('order_index')->default(0);

            $table->string('type')->default('short');
            $table->unsignedInteger('prep_seconds')->default(12);
            $table->unsignedInteger('answer_seconds')->default(45);
            $table->json('rubric_json')->nullable();
            $table->timestamps();
            $table->unique(['interview_id','order_index']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interview_questions');
    }
};
