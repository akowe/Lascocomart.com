<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FcmgOrderItem extends Model
{
    use HasFactory;
     protected $table = 'fcmgorder_items';

    //   public function attributes()
    // {
    //     return $this->hasMany(products::class);
    // }
}
