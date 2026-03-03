<?php

use App\Models\Bookmark;
use App\Models\Tag;
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
        Schema::create('bookmark_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Bookmark::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(Tag::class)->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['bookmark_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookmark_tag');
    }
};
