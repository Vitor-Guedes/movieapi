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
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('budget', 150);
            $table->string('homepage', 150);
            $table->string('original_language', 5);
            $table->string('original_title', 150);
            $table->text('overview');
            $table->string('popularity', 150);
            $table->text('release_date', 50);
            $table->text('revenue', 50);
            $table->text('runtime', 50);
            $table->text('status', 50);
            $table->text('tagline', 50);
            $table->text('title', 150);
            $table->text('vote_average', 150);
            $table->text('vote_count', 150);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
