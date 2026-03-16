<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function index()
    {
        return response()->json(['success'=>true,'data'=>Doctor::all()]);
    }

    public function show($id)
    {
        $doctor = Doctor::findOrFail($id);
        return response()->json(['success'=>true,'data'=>$doctor]);
    }

    public function search(Request $request)
    {
        $query = Doctor::query();

        if ($request->has('specialty')) {
            $query->where('specialty', $request->specialty);
        }
        if ($request->has('city')) {
            $query->where('city', $request->city);
        }

        $doctors = $query->get();
        return response()->json(['success'=>true,'data'=>$doctors]);
    }
}