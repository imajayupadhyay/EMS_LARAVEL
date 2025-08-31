<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = ['name', 'latitude', 'longitude'];


    public function store(Request $request)
{
    $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name'  => 'required|string|max:255',
        'email'      => 'required|email|unique:marketers,email',
        'username'   => 'required|string|unique:marketers,username',
        'password'   => 'required|string|min:6|confirmed',
        'gender'     => 'required|string',
        'contact'    => 'required|string',

        // new location fields
        'location_name' => 'required|string',
        'latitude'      => 'required|numeric',
        'longitude'     => 'required|numeric',
        'radius'        => 'required|integer|min:50', // meters
    ]);

    // Create location first
    $location = Location::create([
        'name'      => $request->location_name,
        'latitude'  => $request->latitude,
        'longitude' => $request->longitude,
        'radius'    => $request->radius,
    ]);

    // Create marketer
    $marketer = Marketer::create([
        'first_name' => $request->first_name,
        'middle_name'=> $request->middle_name,
        'last_name'  => $request->last_name,
        'email'      => $request->email,
        'username'   => $request->username,
        'password'   => Hash::make($request->password),
        'dob'        => $request->dob,
        'doj'        => $request->doj,
        'gender'     => $request->gender,
        'marital_status' => $request->marital_status,
        'contact'    => $request->contact,
        'emergency_contact' => $request->emergency_contact,
        'address'    => $request->address,
        'zip'        => $request->zip,
        'pay_scale'  => $request->pay_scale,
        'work_location' => $request->work_location,
        'location_id'   => $location->id,
    ]);

    return response()->json([
        'message' => 'Marketer registered successfully!',
        'marketer' => $marketer,
        'location' => $location
    ], 201);
}
}

