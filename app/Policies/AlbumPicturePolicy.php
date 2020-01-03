<?php

namespace App\Policies;

use App\User;
use App\AlbumPicture;
use Illuminate\Auth\Access\HandlesAuthorization;

class AlbumPicturePolicy
{
    use HandlesAuthorization;

    public function before($user, $ability)
    {
        if ($user->is_admin) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the album picture.
     *
     * @param  \App\User  $user
     * @param  \App\AlbumPicture  $albumPicture
     * @return mixed
     */
    public function view(User $user, AlbumPicture $albumPicture)
    {
        //
    }

    /**
     * Determine whether the user can create album pictures.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the album picture.
     *
     * @param  \App\User  $user
     * @param  \App\AlbumPicture  $albumPicture
     * @return mixed
     */
    public function update(User $user, AlbumPicture $albumPicture)
    {
        //
    }

    /**
     * Determine whether the user can delete the album picture.
     *
     * @param  \App\User  $user
     * @param  \App\AlbumPicture  $albumPicture
     * @return mixed
     */
    public function delete(User $user, AlbumPicture $albumPicture)
    {
        //
    }

    /**
     * Determine whether the user can restore the album picture.
     *
     * @param  \App\User  $user
     * @param  \App\AlbumPicture  $albumPicture
     * @return mixed
     */
    public function restore(User $user, AlbumPicture $albumPicture)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the album picture.
     *
     * @param  \App\User  $user
     * @param  \App\AlbumPicture  $albumPicture
     * @return mixed
     */
    public function forceDelete(User $user, AlbumPicture $albumPicture)
    {
        //
    }
}
