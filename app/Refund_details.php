<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Refund_details extends Model
{
    protected $guarded = [];
    
    public function refund()
    {
        return $this->belongsTo(Refunds::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
