<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Marketer;
use App\Models\MarketerLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class MarketerController extends Controller
{
    // List all marketers
    public function index()
    {
         $marketers = Marketer::with('locations')->get();

    return Inertia::render('Admin/Marketers/Index', [
        'marketers' => $marketers
    ]);
    }

    // Store new marketer with initial location
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:marketers,email',
            'username'   => 'required|string|unique:marketers,username',
            'password'   => 'required|string|min:6|confirmed',
            'gender'     => 'required|string',
            'contact'    => 'required|string',
            'latitude'   => 'required|numeric',
            'longitude'  => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 1. Create marketer
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
        ]);

        // 2. Save initial location
        MarketerLocation::create([
            'marketer_id' => $marketer->id,
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude,
            'recorded_at' => now(),
        ]);

        return response()->json([
            'message' => 'Marketer registered successfully!',
            'marketer' => $marketer
        ], 201);
    }

    // Show marketer details
    public function show($id)
    {
        return response()->json(Marketer::with('locations')->findOrFail($id));
    }

    // Update marketer
    public function update(Request $request, $id)
    {
        $marketer = Marketer::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:marketers,email,' . $marketer->id,
            'username'   => 'required|string|unique:marketers,username,' . $marketer->id,
            'gender'     => 'required|string',
            'contact'    => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $marketer->update($request->except(['password']));

        if ($request->filled('password')) {
            $marketer->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return response()->json([
            'message' => 'Marketer updated successfully!',
            'marketer' => $marketer
        ]);
    }

    // Delete marketer
    public function destroy($id)
    {
        $marketer = Marketer::findOrFail($id);
        $marketer->delete();

        return response()->json(['message' => 'Marketer deleted successfully!']);
    }
}
