<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wallet extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'wallet';
    // public function initiator() {
    //     return $this->belongsTo(User::class, 'sender_id');
    // }

    // public function receiver() {
    //     return $this->belongsTo(User::class, 'receiver_id');
    // }
}
