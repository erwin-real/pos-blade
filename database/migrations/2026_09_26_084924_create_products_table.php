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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category_id');
            $table->string('desc')->nullable();
            $table->unsignedInteger('stocks');
            $table->decimal('price', 10, 2);
            $table->decimal('srp', 10, 2);
            $table->timestamp('expired_at')->nullable();
            $table->unsignedInteger('procurement');
            $table->string('cover_image');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
