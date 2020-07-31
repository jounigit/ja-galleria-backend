<?php

namespace App\Http\Controllers;

use App\Picture;
use App\Http\Resources\PictureResource;
use App\Http\Resources\PictureCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\BaseController;
use JD\Cloudder\Facades\Cloudder;

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
     * @param array $data
     * @return Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'image' => 'image|mimes:jpeg,jpg,png,gif|max:8000',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $picture = new Picture; // Picture model
        $current_time = time();

        if ($request->hasFile('image')) {

            Cloudder::upload($request->file('image')->getPathname(), $current_time);

            $picId = Cloudder::getPublicId();

            $picture['image'] = Cloudder::getResult()['secure_url'];
            $picture['thumb'] = $picId;
            $picture['title'] = $request->file('image')->getClientOriginalName();
            $picture['user_id'] = Auth::id();
        }


        if (!$picture->save()) {
            return $this->sendError(['Picture creating failed.', 500]);
        }

        return $this->sendResponse($picture, 'Picture stored successfully.');
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
        $this->authorize('update', $picture);

        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

	    $reqData = $request->all();
        $reqData['slug'] = str_slug($request->title);

	if (!$picture->update($reqData)) {
            return $this->sendError(['Picture updating failed.', 500]);
        }

	$updatedPicture = new PictureResource($picture);

        return $this->sendResponse($updatedPicture, 'Picture updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Picture  $picture
     * @return \Illuminate\Http\Response
     */
    public function destroy(Picture $picture)
    {
        $this->authorize('delete', $picture);

        $pic = new PictureResource($picture);

        if ($picture->forceDelete()) {
            Cloudder::destroyImage($pic->thumb, '');
            Cloudder::delete($pic->thumb, '');
            return $this->sendResponse($picture, 'Picture deleted!');
        }

        return $this->sendError(['Picture deleting failed.', 500]);
    }

}
