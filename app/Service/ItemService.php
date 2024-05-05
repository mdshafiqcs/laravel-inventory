<?php 

namespace App\Service;

use App\Exceptions\GeneralException;
use App\Exceptions\NotFoundException;
use App\Helper\CommonHelper;
use App\Models\Inventory;
use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemService {
    
    static public function checkCreateItem(Request $request, int $userId) {
        $inventory = Inventory::findOne($request->inventory_id, $userId);
        if(!$inventory){
            throw new NotFoundException('Inventory not found with this inventory id');
        }
        if($request->qty <= 0){
            throw new GeneralException('Item quantity must be greater than zero');
        }
    }

    static public function createItem(Request $request) {

        try {

            DB::beginTransaction();
           
            $item = new Item();
            $item->inventory_id = $request->inventory_id;
            $item->name = $request->name;
            $item->description = $request->description;
            $item->qty = $request->qty;

            $item->save();

            $imagePath = CommonHelper::saveImage($request->file("image"), 'item_image');
            $item->image = $imagePath;
            $item->save();

            DB::commit();

            return $item;
            
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

    }

   
    static public function deleteItem($id) {
        try {
           
            $item = Item::find($id);
            if(!$item){
                return false;
            }

            CommonHelper::deleteImage($item->image);

            $item->delete();

            return true;
            
        } catch (\Exception $e) {
            throw $e;
        }
    }

    static public function checkUpdateItem(Request $request){
        $item = Item::findById($request->id);
        if(!$item){
            throw new NotFoundException('Item not found with this Item id');
        }
        if($request->qty <= 0){
            throw new GeneralException('Item quantity must be greater than zero');
        }

    }

    static public function updateItem(Request $request) {

        try {
            DB::beginTransaction();

            $item = Item::findById($request->id);
            $item->name = $request->name;
            $item->description = $request->description;
            $item->qty = $request->qty;

            $item->save();

            if($request->image){
                $imagePath = CommonHelper::saveImage($request->file("image"), 'item_image');

                if($imagePath){
                    CommonHelper::deleteImage($item->image);
                    $item->image = $imagePath;
                    $item->save();
                }
            }

            DB::commit();

            return $item;
            
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

    }
}