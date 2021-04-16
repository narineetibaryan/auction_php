<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductBid extends Model
{
    public $fillable = [
        'product_id',
        'user_id',
        'amount'
    ];
}
