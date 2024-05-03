<?php 

namespace App\Service;

use App\Exceptions\GeneralException;
use App\Exceptions\NotFoundException;
use App\Models\Inventory;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

    static public function softDeleteInventory(int $id, int $userId){

        try {
            
            $inventory = Inventory::findOne($id, $userId);
            if(!$inventory){
                throw new NotFoundException('Inventory not found.');
            }

            $inventory->update([
                "is_deleted" => true,
                "updated_at" => Carbon::now(),
            ]);

            return $inventory;
            
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    static public function restoreInventory(int $id, int $userId){

        try {
            
            $inventory = Inventory::findOne($id, $userId, true);
            if(!$inventory){
                throw new NotFoundException('Inventory not found in Trash.');
            }

            $inventory->update([
                "is_deleted" => false,
                "updated_at" => Carbon::now(),
            ]);

            return $inventory;
            
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    static public function deleteInventory(int $id, int $userId){

        try {
            
            $inventory = Inventory::findOne($id, $userId, true);
            if(!$inventory){
                throw new NotFoundException('Inventory not found in Trash');
            }

            $inventory->delete();

            return $inventory;
            
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}