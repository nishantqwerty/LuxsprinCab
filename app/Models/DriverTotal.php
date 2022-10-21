<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverTotal extends Model
{
    protected $table = 'total_payments';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'driver_id', 'id');
    }
}
