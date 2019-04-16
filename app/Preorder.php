<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Preorder extends Model
{
    public function preorder_detail()
    {
        return $this->hasMany(Preorder_detail::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
