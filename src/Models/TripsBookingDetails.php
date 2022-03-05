<?php

namespace Credpal\Expense\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TripsBookingDetails extends Model
{
    use SoftDeletes;

    protected array $guarded = [];
}
