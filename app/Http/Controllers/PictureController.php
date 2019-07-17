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
    private $filename;

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
    protected function setVariables(Int $user_id, $image)
    {
        $this->upload_image = $image;
        $this->image_dir = $user_id . '/images/';
        $this->thumbnail_dir = $user_id . '/thumbnails/';
        $this->filename = time() . '.' . $image->getClientOriginalExtension();
    }

    /**
     * @param mixed $upload
     * @param mixed $images_dir
     * @param mixed $thumbnails_dir
     * @param mixed $filename
     * @return void
     */
    private function handleUpload($upload, $images_dir, $thumbnails_dir, $filename)
    {
        if (!File::exists($images_dir)) {
            File::makeDirectory(public_path($images_dir), 0777, true);
        }
        if (!File::exists($thumbnails_dir)) {
            File::makeDirectory(public_path($thumbnails_dir), 0777, true);
        }

        //Resize image here
        $this->resizeImage($images_dir . $filename, $upload, 600);
        $this->resizeImage($thumbnails_dir . $filename, $upload, 200);
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
            $this->setVariables(Auth::id(), $request->file('image'));

            $this->handleUpload($this->upload_image, $this->image_dir, $this->thumbnail_dir, $this->filename);

            $picture['image'] = $this->image_dir . $this->filename;
            $picture['thumb'] = $this->thumbnail_dir . $this->filename;
        }

        $picture['user_id'] = Auth::id();
        $picture['title'] = $request->title;
        $picture['content'] = $request->content;
        $picture['slug'] = str_slug($request->title);

        if ( ! $picture->save()) {
            return $this->sendError(['Picture creating failed.', 500]);
        }

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
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->hasFile('image')) {
            $this->setVariables(Auth::id(), $request->file('image'));

            $this->handleUpload($this->upload_image, $this->image_dir, $this->thumbnail_dir, $this->filename);
            $updateData['image'] = $this->image_dir . $this->filename;
            $updateData['thumb'] = $this->thumbnail_dir . $this->filename;
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
        $picture->delete();
        if($picture->delete())
        {
            File::delete($picture->image, $picture->thumb);
        }

        return $this->sendResponse($picture, 'Picture deleted!');
    }

    /**
     * Remove file
     */
    private function removeFiles(Picture $picture)
    {
        return File::delete($picture->image, $picture->thumb);
    }
}
