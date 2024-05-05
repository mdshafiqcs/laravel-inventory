<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Validation\ValidationException;
use App\Enum\Status;
use App\Exceptions\GeneralException;
use App\Exceptions\NotFoundException;
use App\Models\Inventory;
use App\Models\Item;
use App\Traits\ResponseTrait;
use App\Service\ItemService;

class ItemController extends Controller
{
    use ResponseTrait;

    public function all($inventoryId) {
        try {

            $items = Item::findByInventoryId($inventoryId);

            return $this->successResponse($items);

        } catch (\Exception $e) {
            return $this->errorResponse("Something went wrong", 500, Status::ERROR);
        }
    }


    public function create(Request $request) {

        try {
            $request->validate([
                'inventory_id' => 'required',
                'name' => 'required',
                'description' => 'required',
                'image' => 'required|mimes:png,jpg,jpeg',
                'qty' => 'required|numeric|min:1',
            ]);

            $user = auth()->user();

            $inventory = Inventory::findOne($request->inventory_id, $user->id);
            if(!$inventory){
                return $this->errorResponse("Inventory not found with this inventory id");
            }

            $item = ItemService::createItem($request);

            return $this->successResponse($item, "Item Added Successfully.");

        } catch(ValidationException $e){
            return $this->errorResponse($e->getMessage(), 422, Status::ERROR);
        } catch (\Exception $e) {
            return $this->errorResponse("Something went wrong", 500, Status::ERROR);
        }
    }

    public function update(Request $request) {

        try {
            $request->validate([
                'id' => 'required',
                'name' => 'required',
                'description' => 'required',
                'image' => 'sometimes|mimes:jpg,png,jpeg',
                'qty' => 'required|numeric|min:1',
            ]);

            $item = Item::find($request->id);
            if(!$item){
                return $this->errorResponse("Item not found with this Item id");
            }
            
            $updatedItem = ItemService::updateItem($request, $item);

            return $this->successResponse($updatedItem, "Item Updated Successfully.");

        } catch(ValidationException $e){
            return $this->errorResponse($e->getMessage(), 422, Status::ERROR);
        } catch (\Exception $e) {
            return $this->errorResponse("Something went wrong", 500, Status::ERROR);
        }
    }



    public function delete($id) {
        try {

            $item = Item::find($id);
            if(!$item){
                return $this->errorResponse("Item not found");
            }

            ItemService::deleteItem($id);

            return $this->successResponse("", "Item Deleted Successfully.");
            
        } catch (\Exception $e) {
            return $this->errorResponse("Something went wrong", 500, Status::ERROR);
        }
    }

}
