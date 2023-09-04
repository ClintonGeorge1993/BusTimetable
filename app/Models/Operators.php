<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operators extends Model
{
    protected $table='Operators';
    public $timestamps = false;
    protected $fillable = ['private_code', 'national_operator_code', 'operator_code', 'operator_short_name', 'operator_name_on_licence', 'trading_name',
        'licence_number', 'licence_classification', 'address1', 'address2', 'address3', 'address4', 'status'];
}
