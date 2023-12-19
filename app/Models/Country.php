<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model {

    use HasFactory;


    public function terms() {
        return $this->hasMany(Term::class, 'countryId');
    }

    public function contact() {
        return $this->hasOne(Contact::class, 'countryId');
    }


    public function contactPhones() {
        return $this->hasMany(ContactPhone::class, 'countryId');
    }




} // end modal
