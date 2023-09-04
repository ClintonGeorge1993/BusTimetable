<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JourneyPatternTimingLinkSequence extends Model
{
    protected $table='JourneyPatternTimingLinkSequence';
    public $timestamps = false;
    protected $fillable = ['from_sequence_number', 'to_sequence_number', 'from_activity', 'to_activity', 'from_dynamic_destination',
        'to_dynamic_destination', 'from_stop_point_id', 'to_stop_point_id', 'from_timing_status', 'to_timing_status','journeypatterntiminglink_id', 'status'];
}
