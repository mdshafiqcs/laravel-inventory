<?php 

namespace App\Service;

use App\Exceptions\GeneralException;
use App\Exceptions\NotFoundException;
use App\Models\Inventory;
use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryService {
    static public function createInventory(Request $request, int $userId){

        try {
            $inventory = Inventory::findByName($request->name, $userId);

            if($inventory){
                throw new GeneralException("Inventory already exists with this name");
            }

            $inventory = Inventory::create([
                "user_id" => $userId,
                "name" => $request->name,
                "description" => $request->description,
                "created_at" => Carbon::now(),
            ]);
            return $inventory;
            
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    static public function updateInventory(Request $request, int $userId){

        try {
            
            $inventory = Inventory::findOne($request->id, $userId);
            if(!$inventory){
                throw new NotFoundException('Inventory not found.');
            }

            $inventory->update([
                "name" => $request->name,
                "description" => $request->description,
                "updated_at" => Carbon::now(),
            ]);

            return $inventory;
            
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    static public function deleteInventory(int $id, int $userId){

        try {
            DB::beginTransaction();
            $inventory = Inventory::findOne($id, $userId, true);
            if(!$inventory){
                throw new NotFoundException('Inventory not found in Trash');
            }

            $items = Item::where("inventory_id", $id)->get();

            foreach ($items as $item) {
                ItemService::deleteItem($item->id);
            }

            $inventory->delete();

            DB::commit();

            return $inventory;
            
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}