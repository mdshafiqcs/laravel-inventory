<?php 

namespace App\Helper;

use Illuminate\Support\Str;

class CommonHelper {
    static public function saveImage($image, string $path = 'public') {

        if($image) {
            $name = Str::random(10) . time() . $image->getClientOriginalName();
            $uploadPath = 'uploads/' . $path . '/';
            $image->move($uploadPath, $name);
            return $uploadPath .  $name;
        }
        return null;
    }

    static public function deleteImage($path){
      
        if(file_exists(public_path().'/'.$path)){
            unlink(public_path().'/'.$path);
        }
    }
}

