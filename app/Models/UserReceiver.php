<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserReceiver extends Model {
    

    public function user() {
        return $this->belongsTo(Country::class, 'userId');
    }


    public function country() {
        return $this->belongsTo(Country::class, 'countryId');
    }

    public function deliveryArea() {
        return $this->belongsTo(DeliveryArea::class, 'deliveryAreaId');
    }




} // end modal
