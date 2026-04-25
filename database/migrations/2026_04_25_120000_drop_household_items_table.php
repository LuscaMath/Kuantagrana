<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $achievementIds = DB::table('achievements')
            ->where('slug', 'casa-organizada')
            ->pluck('id');

        $challengeIds = DB::table('challenges')
            ->where('goal_metric', 'household_items_created')
            ->pluck('id');

        if ($achievementIds->isNotEmpty()) {
            DB::table('user_achievements')->whereIn('achievement_id', $achievementIds)->delete();
            DB::table('achievements')->whereIn('id', $achievementIds)->delete();
        }

        if ($challengeIds->isNotEmpty()) {
            DB::table('user_challenges')->whereIn('challenge_id', $challengeIds)->delete();
            DB::table('challenges')->whereIn('id', $challengeIds)->delete();
        }

        Schema::dropIfExists('household_items');
    }

    public function down(): void
    {
        Schema::create('household_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('environment_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('unit', 30)->default('un');
            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedInteger('minimum_quantity')->default(0);
            $table->date('expires_at')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
};
