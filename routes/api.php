<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\calculator;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// solve equation
Route::post('/solve', [calculator::class, 'solveEquation']);

//get history of equation and solution
Route::get('/getHistory', [calculator::class, 'getHistory']);

//delete record
Route::get('delete/{key}', [calculator::class, 'deleteRecord']);

//save equation and value
Route::post('/save', [calculator::class, 'saveEquationAndValue']);