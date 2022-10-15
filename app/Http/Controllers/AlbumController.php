<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Album;
use App\Http\Requests\AlbumRequest;
use Illuminate\Support\Facades\Storage;

class AlbumController extends Controller
{
    public function store(AlbumRequest $request)
    {
        $image_names = array_values($request->only('image_names'))[0];

        $album = Album::create($request->except('image_names'));

        $this->storeImg($image_names, $album);
    }

    public function update(AlbumRequest $request, $id)
    {
        $image_names = array_values($request->only('image_names'))[0];

        $album = Album::findOrFail($id);

        $this->deleteImg($album);
        $album->update($request->except('image_names'));
        $this->storeImg($image_names, $album);
    }

    private function deleteImg($model)
    {
        foreach ($model->images as $image) {
            // $path = 'storage/images/albums/'.$image?->name;
            $path = preg_replace("/[\s\S]*storage/", '', $image?->name);
            Storage::disk('public')->delete($path);
            $image->delete();
        }
    }


    private function storeImg($image_names, $model)
    {
        foreach ($image_names as $image) {
            $path = $image->storePublicly('images/albums', "public");
            $image =  "/storage/" . $path;
            $model->images()->create(['name' => $image]);
        }
    }

    public function destroy($id)
    {
        $album = Album::findOrFail($id);
        $this->deleteImg($album);
        $album->delete();
    }
}
