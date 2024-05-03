<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Enum\Status;
use App\Models\Inventory;
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

        } catch (\Throwable $th) {
            return $this->errorResponse("Internal Server Error", 500, Status::ERROR);
        }
    }

    public function allDeleted() {
        try {
            
            $userId = auth()->user()->id;

            $inventories = Inventory::findByUserId($userId, true);

            return $this->successResponse($inventories);

        } catch (\Throwable $th) {
            return $this->errorResponse("Internal Server Error", 500, Status::ERROR);
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

        } catch (\Throwable $th) {
            return $this->errorResponse("Internal Server Error", 500, Status::ERROR);
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
                return $this->errorResponse('Inventory not found.', 404);
            }

            $inventory->update([
                "name" => $request->name,
                "description" => $request->description,
                "updated_at" => Carbon::now(),
            ]);

            return $this->successResponse($inventory, "Inventory Updated Successfully.");


        } catch(ValidationException $e){
            return $this->errorResponse($e->getMessage(), 422, Status::ERROR);

        } catch (\Throwable $th) {
            return $this->errorResponse("Internal Server Error", 500, Status::ERROR);
        }
    }

    public function softDelete($id) {
        try {

            $userId = auth()->user()->id;

            $inventory = Inventory::findOne($id, $userId);
            if(!$inventory){
                return $this->errorResponse('Inventory not found.', 404);
            }

            $inventory->update([
                "is_deleted" => true,
                "updated_at" => Carbon::now(),
            ]);

            return $this->successResponse("", "Inventory Moved to Trash Successfully.");
            
        } catch (\Throwable $th) {
            return $this->errorResponse("Internal Server Error", 500, Status::ERROR);
        }
    }

    public function restore($id) {
        try {

            $userId = auth()->user()->id;

            $inventory = Inventory::findOne($id, $userId, true);
            if(!$inventory){
                return $this->errorResponse('Inventory not found in Trash.', 404);
            }

            $inventory->update([
                "is_deleted" => false,
                "updated_at" => Carbon::now(),
            ]);

            return $this->successResponse("", "Inventory Restored from Trash Successfully.");
            
        } catch (\Throwable $th) {
            return $this->errorResponse("Internal Server Error", 500, Status::ERROR);
        }
    }

    public function delete($id) {
        try {

            $userId = auth()->user()->id;

            $inventory = Inventory::findOne($id, $userId, true);
            if(!$inventory){
                return $this->errorResponse('Inventory not found in Trash', 404);
            }

            $inventory->delete();

            return $this->successResponse("", "Inventory Deleted Successfully.");
            
        } catch (\Throwable $th) {
            return $this->errorResponse("Internal Server Error", 500, Status::ERROR);
        }
    }
}
