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
        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('issue_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('path');
            $table->string('thumbnail_path')->nullable();
            $table->string('original_name');
            $table->unsignedInteger('file_size');
            $table->string('mime_type');
            $table->unsignedSmallInteger('width')->nullable();
            $table->unsignedSmallInteger('height')->nullable();
            $table->unsignedTinyInteger('order')->default(0);
            $table->text('caption')->nullable();
            $table->timestamps();
            
            $table->index(['issue_id', 'order']);
        });
        
        // Add photos count to issues table
        Schema::table('issues', function (Blueprint $table) {
            $table->unsignedTinyInteger('photos_count')->default(0)->after('comments_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('issues', function (Blueprint $table) {
            $table->dropColumn('photos_count');
        });
        
        Schema::dropIfExists('photos');
    }
};
