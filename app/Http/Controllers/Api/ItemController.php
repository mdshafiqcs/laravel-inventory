<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Validation\ValidationException;
use App\Enum\Status;
use App\Exceptions\GeneralException;
use App\Exceptions\NotFoundException;
use App\Models\Item;
use App\Traits\ResponseTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use App\Helper\CommonHelper;
use App\Models\Inventory;
use App\Service\ItemService;

class ItemController extends Controller
{
    use ResponseTrait;

    public function all($inventoryId) {
        try {

            $items = Item::findByInventoryId($inventoryId);

            return $this->successResponse($items);

        } catch (\Throwable $th) {
            return $this->errorResponse("Internal Server Error", 500, Status::ERROR);
        }
    }

    public function create(Request $request) {

        $item = new Item();

        try {
            $request->validate([
                'inventory_id' => 'required',
                'name' => 'required',
                'description' => 'required',
                'image' => 'required|mimes:png,jpg,jpeg',
                'qty' => 'required',
                'price' => 'required',
            ]);

            $user = auth()->user();

            ItemService::checkCreateItem($request, $user->id);

            $item = ItemService::createItem($request);

            return $this->successResponse($item, "Item Added Successfully.");

        } catch(ValidationException $e){
            return $this->errorResponse($e->getMessage(), 422, Status::ERROR);
        } catch(NotFoundException $e){
            return $this->errorResponse($e->getMessage(), 404, Status::ERROR);
        } catch(GeneralException $e){
            return $this->errorResponse($e->getMessage(), 400, Status::ERROR);
        } catch (\Throwable $th) {
            return $this->errorResponse("Internal Server Error", 500, Status::ERROR);
        }
    }

    
}
