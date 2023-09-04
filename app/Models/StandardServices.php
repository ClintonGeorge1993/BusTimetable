<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StandardServices extends Model
{
    protected $table='StandardServices';
    public $timestamps = false;
    protected $fillable = ['origin', 'destination', 'service_id', 'status'];
}
