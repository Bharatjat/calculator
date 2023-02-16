<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

// models
use App\Models\calculation;

class calculator extends Controller
{

    // solve equation and return output to user
    function solveEquation(Request $request)
    {

        // Validator
        $data = Validator::make($request->all(), [
            'equation' => 'required',
        ]);

        if ($data->fails()) {
            return [
                'errorMessage' => $data->errors()->all(),
                'status' => 422
            ];
        }

        $validated_data = $data->validated();

        //checking if equation contains any alphabet
        if (preg_match("/[a-z]/i", $validated_data['equation'])) {
            return [
                'errorMessage' => "Equation contain alphabet!",
                'status' => 422
            ];
        }

        // return of result;
        return [
            'data' => $this->parseEq($validated_data['equation']),
            'status' => 200
        ];
    }

    // calculate equation
    function parseEq($eq)
    {

        return eval(strtr('return {eq};', [
            '{eq}' => $eq
        ]));
    }

    // get previous eqration and value
    function getHistory()
    {

        // return of result;
        return [
            'data' => $this->fetchHistory(),
            'status' => 200
        ];
    }

    function fetchHistory()
    {

        return calculation::limit(5)->orderBy('id', 'DESC')->get();
    }

    //to solve and save equation
    function saveEquationAndValue(Request $request)
    {

        $data = $request->all();

        //if equation is not solve
        if (empty($data['previousEquation'])) {

            $equation = $data['userInputEquation'];
            $equation_result = $this->parseEq($data['userInputEquation']);
        } else {

            $equation = $data['previousEquation'];
            $equation_result = $this->parseEq($data['previousEquation']);
        }

        // creating equation and result into json formate
        $json_value = json_encode(['equation' => $equation, 'result' => $equation_result]);

        // saving equation and result
        calculation::create(['value' => $json_value]);

        // return of result;
        return [
            'data' => '',
            'message' => 'save successfully',
            'status' => 200
        ];
    }

    // delete record 
    function deleteRecord($key)
    {

        if ($key == 'all' || $key == 'All') {

            calculation::query()->delete();
        } else {

            calculation::find($key)->delete();
        }

        // return of result;
        return [
            'data' => '',
            'message' => 'deleted successfully',
            'status' => 200
        ];
    }
}
