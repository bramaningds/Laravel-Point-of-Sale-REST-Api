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
        Schema::create('stock_mutations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('product_id');
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->timestamp('mutation_timestamp');
            $table->enum('mutation_type', ['sale.store','sale.update','sale.delete','purchase.store','purchase.update','purchase.delete','adjustment']);
            $table->unsignedDecimal('debet', 8, 2);
            $table->unsignedDecimal('credit', 8, 2);
            $table->unsignedDecimal('balance', 8, 2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_mutations');
    }
};
