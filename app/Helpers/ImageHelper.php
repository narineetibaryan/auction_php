<?php


namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ImageHelper
{
    public function uploadImage($file){
        $name = $file->getClientOriginalName();
        $name = explode('.',$name);
        $ext = end($name);
        $name = Carbon::now()->timestamp . Carbon::now()->micro;
        $path = $name . '.' . $ext;
        Storage::disk('public')->put($path, File::get($file), 'public');
        return 'storage/' . $path;
    }
}
