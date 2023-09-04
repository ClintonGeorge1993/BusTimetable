<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Services extends Model
{
    protected $table='Services';
    public $timestamps = false;
    protected $fillable = ['service_code', 'private_code', 'line_id', 'line_name', 'start_date', 'end_date', 'operator_id', 'mode', 'status'];
}
