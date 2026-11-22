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
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('issue_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('ip_address')->nullable();
            $table->timestamps();
            
            // Prevent duplicate votes
            $table->unique(['issue_id', 'user_id']);
            $table->index(['issue_id', 'created_at']);
        });
        
        // Add vote count to issues table
        Schema::table('issues', function (Blueprint $table) {
            $table->unsignedInteger('votes_count')->default(0)->after('status');
            $table->unsignedInteger('comments_count')->default(0)->after('votes_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('issues', function (Blueprint $table) {
            $table->dropColumn(['votes_count', 'comments_count']);
        });
        
        Schema::dropIfExists('votes');
    }
};
