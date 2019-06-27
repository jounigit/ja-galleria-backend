<?php

use App\Album;
use App\Category;
use App\Picture;
use App\User;
use Illuminate\Database\Seeder;
use SebastianBergmann\CodeCoverage\Report\Xml\Facade;

class AllTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // make categories
        factory(Category::class, 3)->create();
        // Fetch the Category ids
        $categoryIds = Category::all('id')->pluck('id')->toArray();

        // make users
        factory(User::class, 5)->create()->each(function ($user) use ($categoryIds) {
            $randCategory = $categoryIds[array_rand( $categoryIds, 1 )];
            $amountAlbum = random_int(1, 3);
            $amountPicture = random_int(2, 8);
            // update category with this user
            Category::find($randCategory)->update(['user_id' => $user->id]);

            // create albums and pictures and pivot table
            $this->createAlbumsWithPictures($user->id, $randCategory, $amountAlbum, $amountPicture);

        }); /* */
    }

    /**
     * @param Int $userId
     * @param Int $categoryId
     * @param Int $numAlbum
     * @param Int $numPicture
     * @return void
     */
    private function createAlbumsWithPictures($userId, $categoryId, $numAlbum, $numPicture)
    {
        // populate albums
        factory(Album::class, $numAlbum)->create([
            'user_id' => $userId,
            'category_id' => $categoryId
        ]);

        // populate pictures
        factory(Picture::class, $numPicture)->create([
            'user_id' => $userId
        ]);

        // get all albums
        $albums = Album::all();

        Picture::all()->each(function ($picture) use ($albums) {
            $picture->albums()->attach(
                $albums->random(rand( 1, $albums->count() ))->pluck('id')->toArray()
            );
        });
    }
}
