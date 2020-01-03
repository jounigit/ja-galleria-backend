<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AlbumPicture extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'album_picture';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'album_id', 'picture_id',
    ];
}
