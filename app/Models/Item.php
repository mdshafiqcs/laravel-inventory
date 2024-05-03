<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    public function inventory(){
    	return $this->belongsTo(User::class,'inventory_id','id');
    }

    static public function findByInventoryId(int $inventoryId, bool $isDeleted = false){
        $items = Item::where('inventory_id', $inventoryId)->where('is_deleted', $isDeleted)->get();
        return $items;
    }

    static public function findById(int $id, bool $isDeleted = false){
        $item = Item::where('id', $id)->where('is_deleted', $isDeleted)->first();
        return $item;
    }

    

    

}
