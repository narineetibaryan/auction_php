<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public $fillable = [
        'name',
        'description',
        'min_price',
        'image',
        'close_date'
    ];

    public function bid(){
        return $this->hasOne('App\ProductBid', 'product_id')->orderBy('id','desc');
    }
    public function autobid(){
        return $this->hasMany('App\BidBot', 'product_id', 'id');
    }
}
