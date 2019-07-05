<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlbumPictureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('album_picture', function (Blueprint $table) {
            $table->unsignedBigInteger('album_id');
            $table->unsignedBigInteger('picture_id');
            $table->timestamps();

	    $table->foreign('album_id')
                    ->references('id')
                    ->on('albums')
                    ->onDelete('cascade');

	    $table->foreign('picture_id')
                    ->references('id')
                    ->on('pictures')
                    ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('album_picture');
    }
}
