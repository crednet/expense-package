<?php

namespace Credpal\Expense\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TripsTraveller extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    
    public function trips()
    {
        return $this->belongsTo(Trip::class);
    }
}