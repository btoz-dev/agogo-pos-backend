<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\Product;

class Refund extends Model
{
    // protected $fillable = ['order_id', 'preorder_id',];
    protected $guarded = [];

    public function order()
    {
        return $this->hasMany(Order::class);
    }

    public function preorder()
    {
        return $this->hasMany(Preorder::class);
    }

    public function refund_detail()
    {
        return $this->hasMany(Refund_details::class);
    }

}
