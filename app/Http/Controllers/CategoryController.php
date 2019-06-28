<?php

namespace App\Http\Controllers;

use App\Category;
use Auth;
use Validator;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories);
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
            'title' => 'required|max:25',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $category = Category::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'slug' => str_slug($request->title),
            'content' => $request->content,
        ]);

        $response = [
            'success' => true,
            'data' => $category,
            'message' => 'Category stored successfully.'
        ];

        return response()->json($response, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        return response()->json($category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:25',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $reqData = $request->all();
        $reqData['slug'] = str_slug($request->title);

        $category->update($reqData);

        $response = [
            'success' => true,
            'message' => 'Category updated successfully.'
        ];

        return response()->json($response, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $status = $category->delete();

        return response()->json([
            'status' => $status,
            'message' => $status ? 'Category deleted!' : 'Error deleting Category'
        ]);
    }
}
