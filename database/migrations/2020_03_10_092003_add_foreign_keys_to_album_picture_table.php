<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToAlbumPictureTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('album_picture', function(Blueprint $table)
		{
			$table->foreign('album_id')->references('id')->on('albums')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('picture_id')->references('id')->on('pictures')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('album_picture', function(Blueprint $table)
		{
			$table->dropForeign('album_picture_album_id_foreign');
			$table->dropForeign('album_picture_picture_id_foreign');
		});
	}

}
