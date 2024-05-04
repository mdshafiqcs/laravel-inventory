<?php 

namespace App\Helper;

use Illuminate\Support\Str;

class CommonHelper {
    static public function saveImage($image, string $path = 'public') {

        try {
            if($image) {
                $name = Str::random(10) . time() . $image->getClientOriginalName();
                $uploadPath = 'uploads/' . $path . '/';
                $image->move($uploadPath, $name);
                return $uploadPath .  $name;
            }
            return null;

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    static public function deleteImage($path){
      
        try {
            if(file_exists(public_path().'/'.$path)){
                unlink(public_path().'/'.$path);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}

