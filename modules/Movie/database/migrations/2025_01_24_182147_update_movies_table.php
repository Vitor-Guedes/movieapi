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
        Schema::table('movies', function (Blueprint $table) {
            $table->decimal('popularity', 10 , 6)->change();
            $table->string('release_date', 50)->change();
            $table->bigInteger('revenue')->nullable(true)->change();
            $table->integer('runtime')->change();
            $table->string('status', 50)->change();
            $table->string('title', 150)->change();
            $table->decimal('vote_average', 10, 2)->change();
            $table->integer('vote_count')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
