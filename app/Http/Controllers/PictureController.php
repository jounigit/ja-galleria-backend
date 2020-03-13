<?php

namespace App\Http\Controllers;

use App\Picture;
use App\Http\Resources\PictureResource;
use App\Http\Resources\PictureCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\BaseController;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;
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

            // Cloudder::upload($request->file('image'));
            // Cloudder::upload($request->file('image')->getRealPath(), $current_time);
            Cloudder::upload($request->file('image')->getPathname(), $current_time);
            // $cloundary_upload = Cloudder::getResult();

            $picId = Cloudder::getPublicId();
            // $picUrl = $cloundary_upload['url'];

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
        }

        return $this->sendResponse($picture, 'Picture deleted!');
    }

}

// private $upload_image;
// private $image_dir;
// private $thumbnail_dir;
// private $image_path;
// private $thumbnail_path;
// private $images_url;
// private $thumbnails_url;
// private $image_url;
// private $thumbnail_url;
// private $new_filename;

    // /**
    //  * @param mixed $image
    //  * @return void
    //  */
    // protected function setImageProperties($image)
    // {
    //     $this->new_filename = time() . '.' . $image->getClientOriginalExtension();
    //     $this->upload_image = $image;
    //     $this->image_path = $this->image_dir . $this->new_filename;
    //     $this->thumbnail_path = $this->thumbnail_dir . $this->new_filename;
	//     $this->image_url = $this->images_url . $this->new_filename;
    //     $this->thumbnail_url = $this->thumbnails_url . $this->new_filename;
    // }

    // /**
    //  * @param int $user_id
    //  * @return void
    //  */
    // protected function setDirectoryProperties(Int $user_id)
    // {
    //     $image_dir = public_path($user_id . '/images/');
    //     $thumbnail_dir = public_path($user_id . '/thumbnails/');
    //     $this->image_dir = $image_dir;
    //     $this->thumbnail_dir = $thumbnail_dir;
	//     $this->images_url = env('APP_URL','') . $user_id . '/images/';
    //     $this->thumbnails_url = env('APP_URL','') . $user_id . '/thumbnails/';
    // }

    // private function createDirectory($dir)
    // {
    //     if (!File::isDirectory($dir)) {
    //         File::makeDirectory($dir, 0777, true);
    //     }
    // }

    // private function createDirectories()
    // {
    //     $this->createDirectory($this->image_dir);
    //     $this->createDirectory($this->thumbnail_dir);
    // }

    // /**
    //  * Handle uploaded image. Make the directories for user and save the resized images.
    //  *
    //  * @return void
    //  */
    // private function handleUpload()
    // {
    //     //Resize image here
    //     $this->resizeImage($this->image_path, $this->upload_image, 600);
    //     $this->resizeImage($this->thumbnail_path, $this->upload_image, 200);
    // }

    // /**
    //  * @param mixed $image_path
    //  * @param mixed $image
    //  * @param mixed $size
    //  * @return Intervention\Image\Image
    //  */
    // private function resizeImage($image_path, $image, $size)
    // {
    //     Image::make($image)->resize($size, $size, function ($constraint) {
    //         $constraint->aspectRatio();
    //     })->save($image_path);
    // }
