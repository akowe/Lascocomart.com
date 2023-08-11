<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;

class OrderItem extends Model
{
    use HasFactory;
     protected $table = 'order_items';

    //   public function attributes()
    // {
    //     return $this->hasMany(products::class);
    // }
    public function orders()
    {
        return $this->hasMany(Order::class, 'id');
    }
    public function user() {
        return $this->belongsTo(User::class);
    }
    
    public function product() {
        return $this->belongsTo(Product::class);
    }
}
