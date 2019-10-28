<?php

namespace App\Http\Resources;

use App\Category;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\Storage;


class Product extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        // $roles = Category::get();
        // // //$get_role_name = $this->getRoleNames();
        // // $get_role_name = $this->category->pluck('name');
        // foreach ($roles as $role) {
        //     $this->roles->contains($role->id);
        // }
        // $role_name = $get_role_name;
        // $url = Storage::url('file.jpg');

        return [
            'id'            => $this->id,
            'code'          => $this->code,
            'name'          => $this->name,
            'stock'         => $this->stock,
            'price'         => $this->price,
            'category_id'   => $this->category_id,
            'photo'         => 'http://10.254.128.66:82/uploads/product/' . $this->photo,
            // 'cat_name'      => $role
        ];
    }
}
