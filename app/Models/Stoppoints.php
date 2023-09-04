<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stoppoints extends Model
{
    protected $table='Stoppoints';
    public $timestamps = false;
    protected $fillable = ['atco_code', 'common_name', 'longitude', 'latitude', 'stop_type', 'timing_status', 'notes', 'administrative_area_ref', 'locality_id','status'];
}
