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
            $table->integer('type_id');
            $table->integer('family_id');
            $table->foreignId('brand_id')
                ->constrained() // This automatically adds foreign key constraint
                ->onDelete('cascade'); // Optionally, cascade delete
            $table->foreignId('category_id')
                ->constrained() // Assuming a similar setup for category_id
                ->onDelete('cascade');
            $table->string('name');
            $table->string('description');
            // $table->decimal('price', 10, 2);
            $table->string('sku');
            $table->string('inventory');
            $table->integer('weight')->nullable();
            $table->string('dimension')->nullable();
            $table->text('color_id')->nullable();
            $table->text('size_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
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
