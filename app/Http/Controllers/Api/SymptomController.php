<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSymptomRequest;
use App\Http\Requests\UpdateSymptomRequest;
use App\Models\Symptom;
use Illuminate\Support\Facades\Auth;

class SymptomController extends Controller
{
    public function index()
    {
        $symptoms = auth()->user()->symptoms;
        return response()->json(['success'=>true,'data'=>$symptoms]);
    }

   public function store(StoreSymptomRequest $request)
{
    $symptom = Symptom::create([
        'user_id' => Auth::id(), 
        'name' => $request->name,
        'severity' => $request->severity,
        'description' => $request->description,
        'date_recorded' => $request->date_recorded,
        'notes' => $request->notes,
    ]);

    return response()->json($symptom, 201);
}

    public function show($id)
    {
        $symptom = auth()->user()->symptoms()->findOrFail($id);
        return response()->json(['success'=>true,'data'=>$symptom]);
    }

    public function update(UpdateSymptomRequest $request, $id)
    {
        $symptom = auth()->user()->symptoms()->findOrFail($id);
        $symptom->update($request->validated());
        return response()->json(['success'=>true,'data'=>$symptom,'message'=>'Symptôme modifié']);
    }

    public function destroy($id)
    {
        $symptom = auth()->user()->symptoms()->findOrFail($id);
        $symptom->delete();
        return response()->json(['success'=>true,'message'=>'Symptôme supprimé']);
    }
}