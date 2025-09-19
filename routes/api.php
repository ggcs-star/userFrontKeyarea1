<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\LeadController;


Route::post('/user/register', [UserController::class,'register']);
Route::post('/user/login', [UserController::class,'login']);

Route::get('/findProperty', [StateController::class, 'index']);
Route::get('/states/{id}', [StateController::class, 'show']);

Route::get('/budget-50lac-1cr', [ProjectController::class, 'index']);
Route::get('/projects/{id}', [ProjectController::class, 'show']);

Route::get('/2bhk-property', [ProjectController::class, 'getProjectsWith2BHK']);
Route::get('/3bhk-property', [ProjectController::class, 'getProjectsWith3BHK']);
Route::get('/4bhk-property', [ProjectController::class, 'getProjectsWith4BHK']);
Route::get('/5bhk-property', [ProjectController::class, 'getProjectsWith5BHK']);

Route::get('/bungalow', [ProjectController::class, 'getProjectsWithBungalow']);

Route::get('/getFeaturedProjects', [ProjectController::class, 'getFeaturedProjects']);
Route::get('/getemerging_property', [ProjectController::class, 'getemerging_property']);
Route::get('/getemerging_area', [ProjectController::class, 'getemerging_area']);
Route::get('/getLatestProjects', [ProjectController::class, 'getLatestProjects']);


Route::get('/project/{id}', [ProjectController::class, 'show']);

Route::post('/lead/store', [LeadController::class, 'store']);
Route::get('/projects/{id}/similar', [ProjectController::class, 'similarProjects']);

Route::middleware('auth:sanctum')->group(function(): void{
    Route::get('/user', [UserController::class,'me']);
    Route::post('/user/logout', [UserController::class,'logout']);
});


