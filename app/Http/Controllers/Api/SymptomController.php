<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSymptomRequest;
use App\Http\Requests\UpdateSymptomRequest;
use App\Models\Symptom;

class SymptomController extends Controller
{
    public function index()
    {
        $symptoms = auth()->user()->symptoms;
        return response()->json(['success'=>true,'data'=>$symptoms]);
    }

    public function store(StoreSymptomRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $symptom = Symptom::create($data);
        return response()->json(['success'=>true,'data'=>$symptom,'message'=>'Symptôme ajouté']);
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