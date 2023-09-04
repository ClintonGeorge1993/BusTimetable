<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JourneyPatterns extends Model
{
    protected $table='JourneyPatterns';
    public $timestamps = false;
    protected $fillable = ['private_code', 'destination_display', 'direction', 'standard_service_id', 'route_id', 'journey_pattern_section_id', 'status'];
}
