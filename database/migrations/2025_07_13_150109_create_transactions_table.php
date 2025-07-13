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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('location_id')->constrained()->onDelete('cascade');
            $table->foreignId('period_id')->constrained()->onDelete('cascade');
            $table->decimal('qty', 10, 2)->default(0);
            $table->bigInteger('price')->default(0);
            $table->enum('type', ['kredit', 'debit']);
            $table->decimal('amount', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
