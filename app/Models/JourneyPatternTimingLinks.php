<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JourneyPatternTimingLinks extends Model
{
    protected $table='JourneyPatternTimingLinks';
    public $timestamps = false;
    protected $fillable = ['private_code', 'routelink_ref', 'runtime', 'journeypatternsection_id', 'routelink_id', 'status'];
}
