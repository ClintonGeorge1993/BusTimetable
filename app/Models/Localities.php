<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Localities extends Model
{
    protected $table='Localities';
    public $timestamps = false;
    protected $fillable = ['locality_ref', 'locality_name', 'status'];
}
