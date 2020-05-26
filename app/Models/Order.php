<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'status'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function tickets() {
        return $this->hasMany(Ticket::class);
    }
}
