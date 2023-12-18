<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryArea extends Model
{
    use HasFactory;


    public function deliveryTime() {
        return $this->belongsTo(DeliveryTime::class, 'deliveryTimeId');
    }



} // end modal
