<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JourneyPatternSection extends Model
{
    protected $table='JourneyPatternSection';
    public $timestamps = false;
    protected $fillable = ['private_code','status'];
}
