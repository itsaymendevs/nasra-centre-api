<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainCategory extends Model {


    public function subCategories() {
        return $this->hasMany(SubCategory::class, 'mainCategoryId');
    }


    public function types() {
        return $this->hasMany(Type::class, 'mainCategoryId');
    }


    public function products() {
        return $this->hasMany(Product::class, 'mainCategoryId');
    }

} // end modal
