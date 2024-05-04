<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    public function user(){
    	return $this->belongsTo(User::class,'user_id','id');
    }

    public function items(){
        return $this->hasMany(Item::class);
    }

    static public function findByUserId(int $userId) {
        $result = self::where("user_id", $userId)->get();
        return $result;
    }

    static public function findOne(int $id, int $userId) {
        $result = self::where("id", $id)->where("user_id", $userId)->first();
        return $result;
    }

    static public function findManyById(int $id, int $userId) {
        $result = self::where("id", $id)->where("user_id", $userId)->get();
        return $result;
    }

    static public function findByName(string $name, int $userId) {
        $result = self::where("user_id", $userId)->where("name", $name)->first();
        return $result;
    }

}
