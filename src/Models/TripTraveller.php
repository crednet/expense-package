<?php

namespace Credpal\Expense\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TripTraveller extends Model
{
	use SoftDeletes;

	protected $casts = [
		'address' => 'json'
	];

	protected $guarded = [];

	protected $hidden = [
		'updated_at', 'deleted_at'
	];

	public function trips()
	{
		return $this->belongsTo(Trip::class);
	}
}
