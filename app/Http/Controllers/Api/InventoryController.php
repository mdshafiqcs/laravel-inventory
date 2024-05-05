<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Enum\Status;
use App\Models\Inventory;
use App\Models\Item;
use App\Service\ItemService;
use App\Traits\ResponseTrait;
use Carbon\Carbon;

class InventoryController extends Controller
{
    use ResponseTrait;

    public function all() {
        try {
            
            $userId = auth()->user()->id;

            $inventories = Inventory::findByUserId($userId);

            return $this->successResponse($inventories);

        } catch (\Exception $e) {
            return $this->errorResponse("Something went wrong", 500, Status::ERROR);
        }
    }


    public function create(Request $request){

        try {
            $request->validate([
                'name' => 'required',
                'description' => 'required'
            ]);

            $userId = auth()->user()->id;

            $inventory = Inventory::findByName($request->name, $userId);

            if($inventory){
                return $this->errorResponse("Inventory already exists with this name");
            }

            $inventory = Inventory::create([
                "user_id" => $userId,
                "name" => $request->name,
                "description" => $request->description,
                "created_at" => Carbon::now(),
            ]);

            return $this->successResponse($inventory, "Inventory Created Successfully.");

        } catch(ValidationException $e){
            return $this->errorResponse($e->getMessage(), 422, Status::ERROR);
        } catch (\Exception $e) {
            return $this->errorResponse("Something went wrong", 500, Status::ERROR);
        }

    }

    public function update(Request $request){
        try {
            
            $request->validate([
                'id' => 'required',
                'name' => 'required',
                'description' => 'required',
            ]);

            $userId = auth()->user()->id;

            $inventory = Inventory::findOne($request->id, $userId);
            if(!$inventory){
                return $this->errorResponse("Inventory not found");
            }

            $inventory->update([
                "name" => $request->name,
                "description" => $request->description,
                "updated_at" => Carbon::now(),
            ]);

            return $this->successResponse($inventory, "Inventory Updated Successfully.");

        } catch(ValidationException $e){
            return $this->errorResponse($e->getMessage(), 422, Status::ERROR);
        } catch (\Exception $e) {
            return $this->errorResponse("Something went wrong", 500, Status::ERROR);
        }
    }


    public function delete($id) {
        try {

            $userId = auth()->user()->id;

            $inventory = Inventory::findOne($id, $userId);
            if(!$inventory){
                return $this->errorResponse("Inventory not found");
            }

            $items = Item::findByInventoryId($id);

            foreach ($items as $item) {
                ItemService::deleteItem($item->id);
            }

            $inventory->delete();

            return $this->successResponse("", "Inventory Deleted Successfully.");
            
        } catch (\Exception $e) {
            return $this->errorResponse("Something went wrong", 500, Status::ERROR);
        }
    }
}
