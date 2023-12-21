<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model {
    use HasFactory;


    public function country() {
        return $this->belongsTo(Country::class, 'countryId');
    }


    public function areas() {
        return $this->hasMany(DeliveryArea::class, 'stateId');
    }



} // end modal
