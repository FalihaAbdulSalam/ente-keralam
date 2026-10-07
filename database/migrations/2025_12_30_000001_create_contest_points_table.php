<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contest_points', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contest_id')->unique();
            $table->string('contest_type')->nullable();
            $table->integer('participation_points')->default(0);
            $table->integer('bonus_first')->default(0);
            $table->integer('bonus_second')->default(0);
            $table->integer('bonus_third')->default(0);
            $table->timestamps();
        });

        DB::table('contest_points')->insert([
            [
                'contest_id' => 1,
                'contest_type' => 'Reel',
                'participation_points' => 100,
                'bonus_first' => 1000,
                'bonus_second' => 750,
                'bonus_third' => 500,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'contest_id' => 2,
                'contest_type' => 'Reel',
                'participation_points' => 100,
                'bonus_first' => 1000,
                'bonus_second' => 750,
                'bonus_third' => 500,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'contest_id' => 3,
                'contest_type' => 'Photo',
                'participation_points' => 100,
                'bonus_first' => 1000,
                'bonus_second' => 750,
                'bonus_third' => 500,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'contest_id' => 4,
                'contest_type' => 'Essay',
                'participation_points' => 75,
                'bonus_first' => 1000,
                'bonus_second' => 750,
                'bonus_third' => 500,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'contest_id' => 5,
                'contest_type' => 'Poem',
                'participation_points' => 75,
                'bonus_first' => 100,
                'bonus_second' => 75,
                'bonus_third' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'contest_id' => 6,
                'contest_type' => 'Video',
                'participation_points' => 100,
                'bonus_first' => 1000,
                'bonus_second' => 750,
                'bonus_third' => 500,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('contest_points');
    }
};
