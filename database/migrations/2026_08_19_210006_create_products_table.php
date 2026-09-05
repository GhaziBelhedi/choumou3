<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['livre', 'fourniture'])->default('livre');
            $table->string('title', 255);
            $table->string('slug', 280)->unique();
            $table->string('author', 150)->nullable();
            $table->string('isbn', 20)->nullable()->unique();
            $table->longText('description');
            $table->enum('language', ['fr', 'ar', 'en'])->nullable();
            $table->unsignedInteger('pages')->nullable();
            $table->foreignId('publisher_id')->nullable()->constrained('publishers')->nullOnDelete();
            $table->date('publication_date')->nullable();
            $table->decimal('price', 8, 2);
            $table->decimal('compare_at_price', 8, 2)->nullable();
            $table->unsignedInteger('stock_quantity')->default(0);
            $table->string('sku', 60)->nullable();
            $table->string('cover_path')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('deal_ends_at')->nullable();
            $table->unsignedInteger('sales_count')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->unsignedInteger('reviews_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->fullText(['title', 'author', 'description']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
