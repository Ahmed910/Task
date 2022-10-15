<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AlbumRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'image_names' => 'required|array',
            'image_names.*' => 'required|image|mimes:png,jpg,jpeg'
        ];
        foreach(config('translatable.locales') as $locale){
          $rules["$locale.name"] = "required|string|between:2,250";
        }
        // dd($rules);
        return $rules;
    }
}
