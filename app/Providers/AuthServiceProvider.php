<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use App\Album;
use App\Category;
use App\Picture;
use App\User;
use App\Policies\AlbumPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\PicturePolicy;
use App\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
        Album::class => AlbumPolicy::class,
        Category::class => CategoryPolicy::class,
        Picture::class => PicturePolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();

       // Passport::$personalAccessClientId('client-id');
    }
}
