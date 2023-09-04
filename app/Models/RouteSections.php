<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouteSections extends Model
{
    protected $table='RouteSections';
    public $timestamps = false;
    protected $fillable = ['private_code','status'];
}
