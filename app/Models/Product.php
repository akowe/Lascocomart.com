<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;

class Product extends Model
{
    use HasFactory;
    
     protected $fillable = [
        'cat_id', 'prod_name', 'quantity', 'prod_brand', 'description',  'old_price', 'price',  'image', 
        'img1', 'img2', 'img3', 'img4', 'seller_id', 'status'
    ];
    protected $guarded=['id'];
   
    public function order() {
        return $this->hasMany(Order::class);
    }
    
}
