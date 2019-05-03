<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use PhpOffice\PhpSpreadsheet\Calculation\Category;

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
        // //$get_role_name = $this->getRoleNames();
        $get_role_name = $this->category->pluck('name');
        // foreach ($roles as $role) {
        //     $this->roles->contains($role->id);
        // }
        $role_name = $get_role_name;

        return [
            'id'            => $this->id,
            'code'          => $this->code,
            'name'          => $this->name,
            'stock'         => $this->stock,
            'price'         => $this->price,
            'category_id'   => $this->category_id,
            'photo'         => public_path('uploads/product/').$this->photo,
            'cat_name'      => $role_name
        ];
    }
}
