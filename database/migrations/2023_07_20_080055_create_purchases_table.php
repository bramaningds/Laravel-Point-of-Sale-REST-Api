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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('supplier_id');
            $table->unsignedfloat('discount')->nullable()->default(0);
            $table->unsignedfloat('promo')->nullable()->default(0);
            $table->unsignedfloat('tax')->nullable()->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->index('user_id');
            $table->index('supplier_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
