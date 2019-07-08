<?php

namespace App\Http\Controllers;

use App\Picture;
use App\Http\Resources\PictureResource;
use App\Http\Resources\PictureCollection;
use Auth;
use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\BaseController;

class PictureController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new PictureCollection(Picture::all());
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
            'image' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $picture = Picture::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'slug' => str_slug($request->title),
            'content' => $request->content,
            'image' => $request->image,
        ]);

        return $this->sendResponse($picture, 'Picture stored successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Picture  $picture
     * @return \Illuminate\Http\Response
     */
    public function show(Picture $picture)
    {
        return new PictureResource($picture);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Picture  $picture
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Picture $picture)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'max:50',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $reqData = $request->all();
        $reqData['slug'] = str_slug($request->title);

        $picture->update($reqData);

        return $this->sendResponse($picture, 'Picture updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Picture  $picture
     * @return \Illuminate\Http\Response
     */
    public function destroy(Picture $picture)
    {
        $picture->delete();

        return $this->sendResponse($picture, 'Picture deleted!');

    }

}
