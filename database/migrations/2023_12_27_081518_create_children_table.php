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
        Schema::create('children', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id');
            $table->foreign('parent_id')->references('id')->on('parents');
            $table->string('name');
            $table->integer('child_age');
            $table->string('child_gender');
            $table->string('child_category');
            $table->integer('child_cognitive_development');
            $table->integer('child_motor_skills_development');
            $table->integer('child_social_development');
            $table->integer('child_emotional_development');
            $table->integer('child_language_and_literacy');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('child');
    }
};
