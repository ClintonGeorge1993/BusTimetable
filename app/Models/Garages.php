<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Garages extends Model
{
    protected $table='Garages';
    public $timestamps = false;
    protected $fillable = ['garage_code', 'garage_name', 'longitude', 'latitude', 'operator_id', 'status'];
}
