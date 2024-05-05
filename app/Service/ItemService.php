<?php 

namespace App\Service;

use App\Helper\UploadHelper;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemService {
    

    static public function createItem(Request $request) {

        try {

            $imagePath = UploadHelper::saveImage($request->file("image"), 'item_image');
            
            $item = new Item();
            $item->inventory_id = $request->inventory_id;
            $item->name = $request->name;
            $item->description = $request->description;
            $item->qty = $request->qty;
            $item->image = $imagePath;

            $item->save();

            return $item;
            
        } catch (\Exception $e) {
            throw $e;
        }

    }

   
    static public function deleteItem($id) {
        try {
           
            $item = Item::find($id);
            if(!$item){
                return false;
            }

            UploadHelper::deleteImage($item->image);

            $item->delete();

            return true;
            
        } catch (\Exception $e) {
            throw $e;
        }
    }


    static public function updateItem(Request $request, Item $item) {

        try {

            $imagePath = null;
            
            if($request->image){
                $imagePath = UploadHelper::saveImage($request->file("image"), 'item_image');

                if($imagePath){
                    UploadHelper::deleteImage($item->image);
                }
            }

            $item->name = $request->name;
            $item->description = $request->description;
            $item->qty = $request->qty;

            if($imagePath){
                $item->image = $imagePath;
            }

            $item->save();

            return $item;
            
        } catch (\Exception $e) {
            throw $e;
        }
    }
}