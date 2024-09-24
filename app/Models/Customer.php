<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable
{
    use HasFactory;

    protected $table = 'customers'; 
    
    protected $primaryKey = 'customer_id';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'address',
        'birthdate',
        'gender',
        'contact_number',
        'driver_license',
        'password',
        'otp',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'birthdate' => 'date',
    ];
}
