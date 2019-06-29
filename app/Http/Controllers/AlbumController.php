<?php

namespace App\Http\Controllers;

use App\Album;
use Auth;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;

class AlbumController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $albums = Album::all();
        return response()->json($albums);
        // return $this->sendResponse($albums, 'Albums retrieved.');
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
            'title' => 'required|max:50',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $album = Album::create([
            'user_id' => Auth::id(),
            'category_id' => $request->gatecory_id,
            'title' => $request->title,
            'slug' => str_slug($request->title),
            'content' => $request->content,
        ]);

        return $this->sendResponse($album, 'Album stored successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Album  $album
     * @return \Illuminate\Http\Response
     */
    public function show(Album $album)
    {
        return response()->json($album, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Album  $album
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Album $album)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'max:50',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $reqData = $request->all();
        $reqData['slug'] = str_slug($request->title);

        $album->update($reqData);

        return $this->sendResponse($album, 'Album updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Album  $album
     * @return \Illuminate\Http\Response
     */
    public function destroy(Album $album)
    {
        $album->delete();

        return $this->sendResponse($album, 'Album deleted!');
    }
}
