<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $table = 'reviews';
    protected $fillable = [
        'user_id',
        'prod_id',
        'user_review',
        'stars_rated'
        ];
    
    public function user(){
        return $this->belongsTo(User::class);
    }
    
    public function review(){
        return $this->belongsTo(Review::class, 'user_id','user_id');
    }
}
