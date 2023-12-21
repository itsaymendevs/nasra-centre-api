<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserReceiver extends Model {
    

    public function user() {
        return $this->belongsTo(User::class, 'userId');
    }



    public function state() {
        return $this->belongsTo(State::class, 'stateId');
    }


    public function deliveryArea() {
        return $this->belongsTo(DeliveryArea::class, 'deliveryAreaId');
    }



    



} // end modal
