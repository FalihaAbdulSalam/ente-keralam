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
    Schema::create('articles', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('articletype_id')->nullable();
        $table->unsignedBigInteger('section_details_id')->nullable();
        $table->string('entitle');
        $table->string('maltitle')->nullable();
        $table->text('endescription')->nullable();
        $table->text('maldescription')->nullable();
        $table->string('poster')->nullable();
        $table->string('banner')->nullable();
        $table->boolean('status')->default(1);
        $table->timestamps();
    });

    // ✅ Add foreign keys later, after confirming tables exist
    Schema::table('articles', function (Blueprint $table) {
        if (Schema::hasTable('article_types')) {
            $table->foreign('articletype_id')->references('id')->on('article_types')->onDelete('set null');
        }
        if (Schema::hasTable('section_details')) {
            $table->foreign('section_details_id')->references('id')->on('section_details')->onDelete('set null');
        }
    });
}

};
