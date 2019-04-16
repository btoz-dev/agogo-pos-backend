<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Preorder_detail extends Model
{
    protected $guarded = [];
    protected $hidden = ['updated_at'];
    public function preorder()
    {
        return $this->belongsTo(Preorder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
