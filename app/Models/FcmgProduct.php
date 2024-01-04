<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FcmgProduct extends Model
{
    use HasFactory;
    
     protected $fillable = [
        'cat_id', 'prod_name', 'quantity', 'prod_brand', 'description',  'old_price', 'price',  'image', 
        'img1', 'img2', 'img3', 'img4', 'seller_id', 'seller_role', 'status'
    ];

    
}
