<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Album;
use App\Http\Requests\AlbumRequest;

class AlbumController extends Controller
{
    public function store(AlbumRequest $request)
    {
        $image_names = array_values($request->only('image_names'))[0];
        
        $album = Album::create($request->except('image_names'));
        foreach ($image_names as $image) {
            $path = $image->storePublicly('albums', "public");
            $image =  "/storage/" . $path;
            $album->images()->create(['name' => $image]);
        }
    }
}
