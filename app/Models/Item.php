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

    static public function findByInventoryId(int $inventoryId){
        $items = Item::where('inventory_id', $inventoryId)->get();
        return $items;
    }
    

}
