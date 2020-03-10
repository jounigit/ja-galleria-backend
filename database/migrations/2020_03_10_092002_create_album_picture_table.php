<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAlbumPictureTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('album_picture', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->bigInteger('album_id')->unsigned()->index('album_picture_album_id_foreign');
			$table->bigInteger('picture_id')->unsigned()->index('album_picture_picture_id_foreign');
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('album_picture');
	}

}
