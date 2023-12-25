<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model {

    use HasFactory;


    public function user() {
        return $this->belongsTo(User::class, 'userId');
    }

    public function receiver() {
        return $this->belongsTo(UserReceiver::class, 'receiverId');
    }




    public function country() {
        return $this->belongsTo(Country::class, 'countryId');
    }

    public function state() {
        return $this->belongsTo(State::class, 'stateId');
    }

    public function deliveryArea() {
        return $this->belongsTo(DeliveryArea::class, 'deliveryAreaId');
    }



    
    public function store() {
        return $this->belongsTo(PickupStore::class, 'storeId');
    }




    public function orderEmployee() {
        return $this->belongsTo(Employee::class, 'orderEmployeeId', 'id');
    }

    public function paymentEmployee () {
        return $this->belongsTo(Employee::class, 'paymentEmployeeId', 'id');
    }

    public function refundEmployee () {
        return $this->belongsTo(Employee::class, 'refundEmployeeId', 'id');
    }





    public function payment() {
        return $this->belongsTo(Payment::class, 'paymentId');
    }





} // end modal
