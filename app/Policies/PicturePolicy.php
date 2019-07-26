<?php

namespace App\Policies;

use App\User;
use App\Picture;
use Illuminate\Auth\Access\HandlesAuthorization;

class PicturePolicy
{
    use HandlesAuthorization;

    public function before($user, $ability)
    {
        if ($user->is_admin) {
            return true;
        }
    }

    /**
     * Determine whether the user can view any pictures.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the picture.
     *
     * @param  \App\User  $user
     * @param  \App\Picture  $picture
     * @return mixed
     */
    public function view(User $user, Picture $picture)
    {
        //
    }

    /**
     * Determine whether the user can create pictures.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the picture.
     *
     * @param  \App\User  $user
     * @param  \App\Picture  $picture
     * @return mixed
     */
    public function update(User $user, Picture $picture)
    {
        return $user->id === $picture->user_id;
    }

    /**
     * Determine whether the user can delete the picture.
     *
     * @param  \App\User  $user
     * @param  \App\Picture  $picture
     * @return mixed
     */
    public function delete(User $user, Picture $picture)
    {
        return $user->id === $picture->user_id;
    }

    /**
     * Determine whether the user can restore the picture.
     *
     * @param  \App\User  $user
     * @param  \App\Picture  $picture
     * @return mixed
     */
    public function restore(User $user, Picture $picture)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the picture.
     *
     * @param  \App\User  $user
     * @param  \App\Picture  $picture
     * @return mixed
     */
    public function forceDelete(User $user, Picture $picture)
    {
        //
    }
}
