<?php

namespace App\Models;

use App\Traits\HasAssetsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Album extends Model implements TranslatableContract
{
    use HasFactory,Translatable,HasAssetsTrait;
    protected $guarded = ['created_at','updated_at'];
   
    public $translatedAttributes = ['name'];


    public function images() {
        return $this->hasMany(Image::class);
    }
}
