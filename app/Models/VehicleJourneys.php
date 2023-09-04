<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleJourneys extends Model
{
    protected $table='VehicleJourneys';
    public $timestamps = false;
    protected $fillable = ['private_code', 'description', 'block_number', 'ticket_machine_service_code', 'journey_code',
        'layover_point_duration', 'layover_point_name', 'layover_latitude', 'layover_longitude', 'vehicle_journey_code',
        'garage_id', 'service_id', 'line_ref', 'journey_pattern_id', 'departure_time', 'status'];
}
