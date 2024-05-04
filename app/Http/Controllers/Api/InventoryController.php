<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Enum\Status;
use App\Exceptions\GeneralException;
use App\Exceptions\NotFoundException;
use App\Models\Inventory;
use App\Service\InventoryService;
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


    public function create(Request $request){

        try {
            $request->validate([
                'name' => 'required',
                'description' => 'required'
            ]);

            $userId = auth()->user()->id;

            $inventory = InventoryService::createInventory($request, $userId);

            return $this->successResponse($inventory, "Inventory Created Successfully.");

        } catch(ValidationException $e){
            return $this->errorResponse($e->getMessage(), 422, Status::ERROR);
        } catch(GeneralException $e){
            return $this->errorResponse($e->getMessage());
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

            $inventory = InventoryService::updateInventory($request, $userId);

            return $this->successResponse($inventory, "Inventory Updated Successfully.");

        } catch(ValidationException $e){
            return $this->errorResponse($e->getMessage(), 422, Status::ERROR);
        } catch(NotFoundException $e){
            return $this->errorResponse($e->getMessage(), 404, Status::ERROR);
        } catch (\Throwable $th) {
            return $this->errorResponse("Internal Server Error", 500, Status::ERROR);
        }
    }


    public function delete($id) {
        try {

            $userId = auth()->user()->id;

            InventoryService::deleteInventory($id, $userId);

            return $this->successResponse("", "Inventory Deleted Successfully.");
            
        } catch ( NotFoundException $e) {
            return $this->errorResponse($e->getMessage(), 404, Status::ERROR);
        } catch (\Throwable $th) {
            return $this->errorResponse("Internal Server Error", 500, Status::ERROR);
        }
    }
}
