<?php

namespace App\Http\Controllers;

use App\Picture;
use App\Http\Resources\PictureResource;
use App\Http\Resources\PictureCollection;
use Auth;
use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\BaseController;
use Intervention\Image\Facades\Image;
use File;

class PictureController extends BaseController
{
    private $upload_image;
    private $image_dir;
    private $thumbnail_dir;
    private $image_path;
    private $thumbnail_path;

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
            'title' => 'max:50',
            'image' => 'image|mimes:jpeg,jpg,png,gif|max:8000',
        ]);
    }

    /**
     * @param int $user_id
     * @param mixed $image
     * @return void
     */
    protected function setProperties(Int $user_id, $image)
    {
        $new_filename = time() . '.' . $image->getClientOriginalExtension();
        $image_dir = public_path($user_id . '/images/');
        $thumbnail_dir = public_path($user_id . '/thumbnails/');
        $this->upload_image = $image;
        $this->image_dir = $image_dir;
        $this->thumbnail_dir = $thumbnail_dir;
        $this->image_path = $image_dir . $new_filename;
        $this->thumbnail_path = $thumbnail_dir . $new_filename;
    }

    /**
     * Handle uploaded image. Make the directories for user and save the resized images.
     *
     * @return void
     */
    private function handleUpload()
    {
        if (!File::isDirectory($this->image_dir)) {
            File::makeDirectory($this->image_dir, 0777, true);
        }
        if (!File::isDirectory($this->thumbnail_dir)) {
            File::makeDirectory($this->thumbnail_dir, 0777, true);
        }

        //Resize image here
        $this->resizeImage($this->image_path, $this->upload_image, 600);
        $this->resizeImage($this->thumbnail_path, $this->upload_image, 200);
    }

    /**
     * @param mixed $image_path
     * @param mixed $image
     * @param mixed $size
     * @return Intervention\Image\Image
     */
    private function resizeImage($image_path, $image, $size)
    {
        Image::make($image)->resize($size, $size, function ($constraint) {
            $constraint->aspectRatio();
        })->save($image_path);
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

        $picture = new Picture;

        if ($request->hasFile('image')) {
            // set class properties.
            $this->setProperties(Auth::id(), $request->file('image'));

            $this->handleUpload();

            $picture['image'] = $this->image_path;
            $picture['thumb'] = $this->thumbnail_path;
        } else {
            $picture['image'] = 'default.jpg';
            $picture['thumb'] = 'default.jpg';
        }

        $picture['user_id'] = Auth::id();
        $picture['title'] = $request->title;
        $picture['content'] = $request->content;
        $picture['slug'] = str_slug($request->title);

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
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->hasFile('image')) {
            // set class properties.
            $this->setProperties(Auth::id(), $request->file('image'));

            $this->handleUpload();
            $updateData['image'] = $this->image_path;
            $updateData['thumb'] = $this->thumbnail_path;
        }

        $updateData['title'] = $request->title;
        $updateData['content'] = $request->content;
        $updateData['slug'] = str_slug($request->title);

        $picture->update($updateData);

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
        // $picture->delete();
        if ($picture->forceDelete()) {
            File::delete($picture->image, $picture->thumb);
        }

        return $this->sendResponse($picture, 'Picture deleted!');
    }
}
