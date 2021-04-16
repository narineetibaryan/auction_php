<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BidBot extends Model
{
    public $fillable = [
        'product_id',
        'user_id'
    ];

    public function user(){
        return $this->belongsTo('App\User');
    }

}
