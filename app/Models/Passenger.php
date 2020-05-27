<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Passenger extends Model
{
    protected $fillable = [
        'name', 'id_type', 'id_num', 'gender', 'mobile', 'type', 'user_id'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
