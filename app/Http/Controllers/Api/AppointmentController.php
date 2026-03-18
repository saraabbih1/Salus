<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Models\Appointment;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = auth()->user()->appointments;
        return response()->json(['success'=>true,'data'=>$appointments]);
    }

    public function store(StoreAppointmentRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $appointment = Appointment::create($data);
        return response()->json(['success'=>true,'data'=>$appointment,'message'=>'Rendez-vous créé']);
    }

    public function show($id)
    {
        $appointment = auth()->user()->appointments()->findOrFail($id);
        return response()->json(['success'=>true,'data'=>$appointment]);
    }

    public function update(UpdateAppointmentRequest $request, $id)
    {
        $appointment = auth()->user()->appointments()->findOrFail($id);
        $appointment->update($request->validated());
        return response()->json(['success'=>true,'data'=>$appointment,'message'=>'Rendez-vous modifié']);
    }

    public function destroy($id)
    {
        $appointment = auth()->user()->appointments()->findOrFail($id);
        $appointment->delete();
        return response()->json(['success'=>true,'message'=>'Rendez-vous annulé']);
    }
}