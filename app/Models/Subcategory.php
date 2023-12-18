<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model {

    use HasFactory;

    public function types() {
        return $this->hasMany(Type::class, 'subCategoryId');
    }


    public function products() {
        return $this->hasMany(Product::class, 'subCategoryId');
    }


} // end modal
