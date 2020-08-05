<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserCollection;
use Validator;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return User::all();
        // return new UserCollection(User::all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $validator = Validator::make($request->all(), [
            'title' => 'max:50',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $data = $request->all();

        $status = $user->update($data);

        return response()->json([
            'status' => $status,
            'message' => $status ? 'User updated!' : 'Error updating User'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $status = $user->delete();

        return response()->json([
            'status' => $status,
            'message' => $status ? 'User Deleted!' : 'Error Deleting User'
        ]);
    }
}
