<?php

namespace App\Http\Controllers;

use App\AlbumPicture;
use App\Album;
use App\Http\Resources\AlbumResource;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Validator;

class AlbumPictureController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return AlbumPicture::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'album_id' => 'required',
            'picture_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        AlbumPicture::create([
            'album_id' => $request->album_id,
            'picture_id' => $request->picture_id,
        ]);

	$album = new AlbumResource(Album::find($request->album_id));

        return $this->sendResponse($album, 'Data stored successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AlbumPicture  $albumPicture
     * @return \Illuminate\Http\Response
     */
    public function show(AlbumPicture $albumPicture)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AlbumPicture  $albumPicture
     * @return \Illuminate\Http\Response
     */
    public function destroy(AlbumPicture $albumPicture)
    {
        $album = new AlbumResource(Album::find($albumPicture->album_id));

        return $this->sendResponse($album, 'Picture removed from album!');
    }
}
