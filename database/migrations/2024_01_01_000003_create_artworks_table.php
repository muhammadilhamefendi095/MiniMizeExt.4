<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artworks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artist_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('exhibition_id')->nullable()->constrained('exhibitions')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('medium')->nullable();
            $table->string('size')->nullable();
            $table->decimal('starting_price', 15, 2)->default(0);
            $table->decimal('current_price', 15, 2)->default(0);
            $table->string('image_path')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'sold'])->default('pending');
            $table->boolean('is_auction')->default(true);
            $table->timestamp('auction_ends_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artworks');
    }
};
