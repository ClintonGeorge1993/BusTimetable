<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouteLinksMapping extends Model
{
    protected $table='RouteLinksMapping';
    public $timestamps = false;
    protected $fillable = ['latitude', 'longitude', 'routelink_id', 'status'];
}
