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
        Schema::create('toys_descriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->string('description');
            $table->string('category');
            $table->integer('age');
            $table->string('gender');
            $table->integer('cognitive_development');
            $table->integer('motor_skills_development');
            $table->integer('social_development');
            $table->integer('emotional_development');
            $table->integer('language_and_literacy');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('toy_description');
    }
};
