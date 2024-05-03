<?php 

namespace App\Service;

use App\Exceptions\GeneralException;
use App\Exceptions\NotFoundException;
use App\Helper\CommonHelper;
use App\Models\Inventory;
use App\Models\Item;
use Illuminate\Http\Request;


class ItemService {
    
    static public function checkCreateItem(Request $request, int $userId) {
        $inventory = Inventory::findOne($request->inventory_id, $userId);
        if(!$inventory){
            throw new NotFoundException('Inventory not found with this inventory id');
        }
        if($request->qty <= 0){
            throw new GeneralException('Item quantity must be greater than zero');
        }
        if($request->price <= 0){
            throw new GeneralException('Item price must be greater than zero');
        }
    }

    static public function createItem(Request $request) {

        try {
            $imagePath = CommonHelper::saveImage($request->file("image"), 'item_image');

            $item = new Item();
            $item->inventory_id = $request->inventory_id;
            $item->name = $request->name;
            $item->description = $request->description;
            $item->image = $imagePath;
            $item->qty = $request->qty;
            $item->price = $request->price;

            if(isset($request->min_stock)){
                $item->min_stock = $request->min_stock;
            }

            $item->save();
            return $item;
            
        } catch (\Throwable $th) {
            throw $th;
        }

    }
}