<?php

namespace App\Services\Parameter;

use App\Http\Resources\Parameter\ParameterValueResource;
use App\Models\Parameter;
use App\Models\ParameterValue;

class ParameterService{

    public function getAllParameters(int $parameterOrder){
        $allParamters = ParameterValue::where('parameter_order', '=', $parameterOrder)->get();

        return response()->json([
            'allParamteterData' => ParameterValueResource::collection($allParamters)
        ], 200);

    }

    public function createParameter(array $parameterData){

        $parameter = Parameter::find($parameterData['parameterId']);

        $multipleSelect = $parameterData['multipleSelect']??"";

        $newparamteterValues = ParameterValue::create([
            'parameter_id' => $parameterData['parameterId'],
            'parameter_order' => $parameterData['parameterOrder'],
            'parameter_value' => $parameterData['parameterValue'],
            'description' => $parameterData['description'],
            'internal_code'=>$parameterData['internalCode'],
            'multiple_select'=>$multipleSelect,
        ]);

        return response()->json([
            'message' => 'parameter has been created !'
        ], 200);

    }

    public function editParameter(int $paramteterId){
        $parameterValues = ParameterValue::find($paramteterId);

        return response()->json([
            'parameterValues' => new ParameterValueResource($parameterValues)
        ], 200);

    }

    public function updateParameter(array $parameterData){
        $paramteterValues = ParameterValue::find($parameterData['parameterValueId']);

        $multipleSelect = $parameterData['multipleSelect']??"";
        $paramteterValues->fill([
            'parameter_value' => $parameterData['parameterValue'],
            'description' => $parameterData['description'],
            'internal_code'=>$parameterData['internalCode'],
            'multiple_select'=>$multipleSelect,
        ]);

        $paramteterValues->save();

        return response()->json([
            'message' => 'parameter has been updated !'
        ], 200);

    }

    public function deleteParameter(int $parameterId){
        $parameter = ParameterValue::find($parameterId);
        $parameter->delete();
        return response()->json([
            'message' => 'parameter has been deleted!'
        ], 200);

    }

}
