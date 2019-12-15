<?php

namespace App\Http\Controllers;

use App\Category;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CategoryCollection;
use Auth;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;

class CategoryController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Category::with('user', 'albums')->get();
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

	$newCategory = new CategoryResource($category);

        return $this->sendResponse($newCategory, 'Category stored successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        return new CategoryResource($category);
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
        $this->authorize('update', $category);

        $validator = Validator::make($request->all(), [
            'title' => 'required|max:25',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $reqData = $request->all();
        $reqData['slug'] = str_slug($request->title);

        $category->update($reqData);

	    $updatedCategory = new CategoryResource($category);

        return $this->sendResponse($updatedCategory, 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);
        $category->delete();

        return $this->sendResponse($category, 'Category deleted!');
    }
}
