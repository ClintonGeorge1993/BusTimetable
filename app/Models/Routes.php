<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Routes extends Model
{
    protected $table='Routes';
    public $timestamps = false;
    protected $fillable = ['private_code', 'description', 'route_section_id', 'status'];
}
