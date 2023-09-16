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
            $table->string('name', 100)->unique();
            $table->string('description', 1000)->nullable();
            $table->unsignedDecimal('price', 8, 2);
            $table->unsignedDecimal('stock', 8, 2);
            $table->unsignedinteger('category_id');
            $table->enum('sellable', ['Y', 'N']);
            $table->enum('purchasable', ['Y', 'N']);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['description', 'category_id', 'sellable', 'purchasable']);
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
