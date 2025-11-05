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
        Schema::create('category_post', function (Blueprint $table) {
            $table->id();
            $table->uuid('post_uuid');
            $table->uuid('category_uuid');
            $table->timestamps();

            $table->foreign('post_uuid')->references('uuid')->on("posts")->cascadeOnDelete();
            $table->foreign('category_uuid')->references('uuid')->on("categories")->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_post');
    }
};
