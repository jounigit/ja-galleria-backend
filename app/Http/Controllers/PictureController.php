<?php

namespace App\Http\Controllers;

use App\Picture;
use Auth;
use Illuminate\Http\Request;
use Validator;

class PictureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pictures = Picture::all();
        return response()->json($pictures);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $picture = Picture::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'slug' => str_slug($request->title),
            'content' => $request->content,
            'image' => $request->image,
        ]);

        $response = [
            'success' => true,
            'data' => $picture,
            'message' => 'Picture stored successfully.'
        ];

        return response()->json($response, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Picture  $picture
     * @return \Illuminate\Http\Response
     */
    public function show(Picture $picture)
    {
        return response()->json($picture, 200);
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
        /* $data = [
            'user_id' => Auth::id(),
            'title' => $request->title,
            'slug' => str_slug($request->title),
            'content' => $request->content,
            'image' => $request->image,
        ]; */
        $reqData = $request->all();
        $reqData['slug'] = str_slug($request->title);

        $picture->update($reqData);

        $response = [
            'success' => true,
            'message' => 'Picture updated successfully.'
        ];

        return response()->json($response, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Picture  $picture
     * @return \Illuminate\Http\Response
     */
    public function destroy(Picture $picture)
    {
        $status = $picture->delete();

        return response()->json([
            'status' => $status,
            'message' => $status ? 'Picture deleted!' : 'Error deleting Picture'
        ]);
    }

}
