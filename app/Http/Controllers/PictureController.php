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
            'title' => 'required|max:50',
            'image' => 'required|image|mimes:jpeg,jpg,png,gif|max:8000',
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

        $images_dir = Auth::id() . '/images/';
        $thumbnails_dir = Auth::id() . '/thumbnails/';

        $uploaded_file = $request->file('image');
        $filename = 'image-' . time() . '.' . $uploaded_file->getClientOriginalExtension();

        $this->handleUpload($uploaded_file, $images_dir, $thumbnails_dir, $filename);

        $picture = Picture::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'slug' => str_slug($request->title),
            'content' => $request->content,
            'image' => $images_dir . $filename,
            'thumb' => $thumbnails_dir . $filename
        ]);

        return $this->sendResponse($picture, 'Picture stored successfully.');
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

        if ($validator->fails()) {
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
