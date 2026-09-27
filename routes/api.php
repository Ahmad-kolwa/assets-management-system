<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\AssetController;
use App\Http\Controllers\Api\ServiceRequestController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\HodController;



//
// Authentication
//
Route::post('/login', [AuthController::class, 'login']);
//Route::post('/assets', [AssetController::class, 'store']);

Route::middleware('auth:api')->group(function () {
    

    //
    // Profile
    //
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);

    Route::post('/logout', [AuthController::class, 'logout']);
  Route::get('/assets', [AssetController::class, 'index']);
      
       // Admin only
    Route::middleware('role:admin')->group(function () {
        Route::post('/assets', [AssetController::class, 'store']);
       // Route::get('/assets', [AssetController::class, 'index']);
        Route::get('/assets/{id}', [AssetController::class, 'show']);
       Route::put('/assets/{id}', [AssetController::class, 'update']);
        Route::delete('/assets/{id}', [AssetController::class, 'destroy']);

        // Categories
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::get('/categories', [CategoryController::class, 'index']);

        //request
            Route::put('/service-requests/{id}/complete', [ServiceRequestController::class, 'complete']);

              // User Management
    Route::get('/users', [AuthController::class, 'users']);
            Route::put('/users/{id}/role', [AuthController::class, 'assignRole']);
            Route::put('/users/{id}/deactivate', [AuthController::class, 'deactivate']);
                Route::put('/users/{id}/activate', [AuthController::class, 'activate']);


                // Departments
Route::get('/departments', [DepartmentController::class, 'index']);
Route::post('/departments', [DepartmentController::class, 'store']);
Route::put('/departments/{id}', [DepartmentController::class, 'update']);
Route::delete('/departments/{id}', [DepartmentController::class, 'destroy']);

    });

    // HOD only
Route::middleware('role:hod')->group(function () {

    Route::get('/hod/assets', [HodController::class, 'assets']);

    Route::get('/hod/staff', [HodController::class, 'staff']);

    Route::get('/hod/requests', [HodController::class, 'requests']);

    Route::get('/hod/reports', [HodController::class, 'reports']);

});


    // Admin and HOD
    Route::middleware('role:admin,hod')->group(function () {
      Route::get('/service-requests', [ServiceRequestController::class, 'index']);
         Route::put('/service-requests/{id}/approve', [ServiceRequestController::class, 'approve']);
    Route::put('/service-requests/{id}/reject', [ServiceRequestController::class, 'reject']);
Route::get('/reports', [AssetController::class, 'reports']);
    });

    // Employee only
     Route::middleware('role:employee')->group(function () {
    Route::post('/service-requests', [ServiceRequestController::class, 'store']);
    Route::get('/service-requests/my', [ServiceRequestController::class, 'myRequests']);
     });


});