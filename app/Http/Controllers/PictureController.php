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
    private $images_url;
    private $thumbnails_url;
    private $image_url;
    private $thumbnail_url;

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
     * @param mixed $image
     * @return void
     */
    protected function setImageProperties($image)
    {
        $new_filename = time() . '.' . $image->getClientOriginalExtension();
        $this->upload_image = $image;
        $this->image_path = $this->image_dir . $new_filename;
        $this->thumbnail_path = $this->thumbnail_dir . $new_filename;
	$this->image_url = $this->images_url . $new_filename;
        $this->thumbnail_url = $this->thumbnails_url . $new_filename;
    }

    /**
     * @param int $user_id
     * @return void
     */
    protected function setDirectoryProperties(Int $user_id)
    {
        $image_dir = public_path($user_id . '/images/');
        $thumbnail_dir = public_path($user_id . '/thumbnails/');
        $this->image_dir = $image_dir;
        $this->thumbnail_dir = $thumbnail_dir;
	$this->images_url = 'http://localhost:8000/' .$user_id . '/images/';
        $this->thumbnails_url = 'http://localhost:8000/' .$user_id . '/thumbnails/';
    }

    private function createDirectory($dir)
    {
        if (!File::isDirectory($dir)) {
            File::makeDirectory($dir, 0777, true);
        }
    }

    private function createDirectories()
    {
        $this->createDirectory($this->image_dir);
        $this->createDirectory($this->thumbnail_dir);
    }

    /**
     * Handle uploaded image. Make the directories for user and save the resized images.
     *
     * @return void
     */
    private function handleUpload()
    {
        // if (!File::isDirectory($this->image_dir)) {
        //     File::makeDirectory($this->image_dir, 0777, true);
        // }
        // if (!File::isDirectory($this->thumbnail_dir)) {
        //     File::makeDirectory($this->thumbnail_dir, 0777, true);
        // }

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

        // set class properties.
        $this->setDirectoryProperties(Auth::id());
        // create directories for user's pictures.
        $this->createDirectories();

        $picture = new Picture;

        if ($request->hasFile('image')) {
            // set class properties.
            $this->setImageProperties($request->file('image'));
            // make thumbnail, resize and save pictures.
            $this->handleUpload();

            $picture['image'] = $this->image_url;
            $picture['thumb'] = $this->thumbnail_url;
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
        $this->authorize('update', $picture);

        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

	$reqData = $request->all();
        $reqData['slug'] = str_slug($request->title);

        if ($request->hasFile('image')) {
            // set class properties.
            $this->setDirectoryProperties(Auth::id());
            $this->createDirectories();
            $this->setImageProperties($request->file('image'));
            // make thumbnail, resize and save pictures.
            $this->handleUpload();

            $reqData['image'] = $this->image_path;
            $reqData['thumb'] = $this->thumbnail_path;
        }

    /**
    $updateData['title'] = $request->title;
        $updateData['content'] = $request->content;
        $updateData['slug'] = str_slug($request->title);
*/

        $picture->update($reqData);

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

        if ($picture->forceDelete()) {
            File::delete($picture->image, $picture->thumb);
        }

        return $this->sendResponse($picture, 'Picture deleted!');
    }

}
