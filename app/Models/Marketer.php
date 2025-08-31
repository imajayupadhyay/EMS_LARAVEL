<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Marketer extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'marketers';

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'username',
        'password',
        'dob',
        'doj',
        'gender',
        'marital_status',
        'contact',
        'emergency_contact',
        'address',
        'zip',
        'pay_scale',
        'work_location',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // ✅ Relation with marketer_locations
    public function locations()
    {
        return $this->hasMany(MarketerLocation::class, 'marketer_id');
    }
}
