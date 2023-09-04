<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouteLinks extends Model
{
    protected $table='RouteLinks';
    public $timestamps = false;
    protected $fillable = ['private_code', 'route_section_id', 'from_stop_point_id', 'to_stop_point_id', 'distance', 'direction', 'status'];
}
