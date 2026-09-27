<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceRequest;
use App\Models\Asset;


class ServiceRequestController extends Controller
{
       // Admin and HOD view all requests
    public function index()
    {
   
        $requests = ServiceRequest::with(['user', 'asset'])->get();

        return response()->json([
            'success' => true,
            'data' => $requests,
        ]);
    }

    //one method in which employee view own request while Admin and HOD view all requests
    public function index()
{
    $user = auth()->user();

    $query = ServiceRequest::with(['user', 'asset']);
///akitokea employee ataona request zake tu zenye id yake
    if ($user->role === 'employee') {
        $query->where('user_id', $user->id);
    }
//asipotokea naamanisha akitokea admin au hod wataona zote 
    $requests = $query->get();

    return response()->json([
        'success' => true,
        'data' => $requests,
    ]);
}      

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Employee creates a service request
   
        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'description' => 'required|string',
        ]);

        $serviceRequest = ServiceRequest::create([
            'user_id' => $request->user()->id, //auth()->user()->id or auth()->id  dont include request when you are using auth
            'asset_id' => $validated['asset_id'],
            'description' => $validated['description'],
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Service request submitted successfully.',
            'data' => $serviceRequest,
        ], 201);
     }



        // Employee views own requests
    public function myRequests(Request $request)
    {
        //Get only service requests whose user_id is equal to the ID of the currently logged-in user.
        $requests = ServiceRequest::with(['user','asset'])
            ->where('user_id', $request->user()->id)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $requests,
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function approve($id)
{
    $request = ServiceRequest::findOrFail($id);
 // $request->status = 'approved';
    // $request->save();
    $request->update([
    'status' => 'approved'
]);

    return response()->json([
        'success' => true,
        'message' => 'Service request approved successfully.',
        'data' => $request,
    ]);
}

///if there are more than one department and hods approve their own department you will add condition
public function approve($id)
{
    $request = ServiceRequest::with('asset')->findOrFail($id);

    $hod = auth()->user();

    if (
        $user->role === 'hod' &&
        $request->asset->department_id !== $hod->department_id
    ) {
        return response()->json([
            'success' => false,
            'message' => 'You can only manage requests from your department.'
        ], 403);
    }

    $request->update([
        'status' => 'approved'
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Service request approved successfully.',
        'data' => $request,
    ]);
}


public function reject($id)
{
    $request = ServiceRequest::findOrFail($id);

    // $request->status = 'rejected';
    // $request->save();
 $request->update([
    'status' => 'rejected'
]);
    return response()->json([
        'success' => true,
        'message' => 'Service request rejected successfully.',
        'data' => $request,
    ]);
}

public function complete($id)
{
    $serviceRequest = ServiceRequest::findOrFail($id);

    // Mark service request as completed
     $serviceRequest->update([
        'status' => 'completed'
    ]);

    // Update the related asset
    $asset =  $serviceRequest->asset;

    $asset->update([
        'status' => 'available'
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Service request completed successfully.',
        'data' => $serviceRequest,
    ]);
}
/*
Challenge 1 — HOD approving a service request

Your system has:

Users

id
name
role
department_id
is_active

Assets

id
name
department_id
status

Service Requests

id
user_id
asset_id
description
status

The interviewer gives you this requirement:

A HOD can approve a service request only if:

The logged-in user is a HOD.
The HOD is active.
The service request exists.
The service request is currently pending.
The asset attached to the request belongs to the HOD's department.
The asset must currently be maintenance.
If all conditions pass, change the request status to approved.
Otherwise, return an appropriate error.
///////////////////////////////////ANSWER//////////////////////////////////////////////
public function approve($id)
{
    // 1. Get the logged-in HOD
    $hod = auth('api')->user();

    // 2. Check if HOD is active
    if (!$hod->is_active) {
        return response()->json([
            'success' => false,
            'message' => 'Your account is inactive',
        ], 403);
    }

    // 3. Find the service request and its asset
    $serviceRequest = ServiceRequest::with('asset')->findOrFail($id);

    // 4. Check if request is still pending
    if ($serviceRequest->status !== 'pending') {
        return response()->json([
            'success' => false,
            'message' => 'Only pending requests can be approved',
        ], 400);
    }

    // 5. Check that the asset belongs to HOD's department
    if ($serviceRequest->asset->department_id !== $hod->department_id) {
        return response()->json([
            'success' => false,
            'message' => 'You can only approve requests from your department',
        ], 403);
    }

    // 6. Check that asset is under maintenance
    if ($serviceRequest->asset->status !== 'maintenance') {
        return response()->json([
            'success' => false,
            'message' => 'Only assets under maintenance can be approved',
        ], 400);
    }

    // 7. Approve the request
    $serviceRequest->update([
        'status' => 'approved',
    ]);

    // 8. Return success response
    return response()->json([
        'success' => true,
        'message' => 'Service request approved successfully',
        'data' => $serviceRequest,
    ], 200);
}

///////////////////////////////QUESTION////////////////////////////////////////////
Create an API that allows an employee to submit a service request for an asset.

Endpoint:

POST /api/service-requests

But there are several business rules.

Rules
The user must be authenticated.
The user must have the role employee.
The employee must have an active account.
The asset must exist.
The asset must belong to the same department as the employee.
The asset must currently have status assigned.
The employee cannot create another pending service request for the same asset.
The description must contain at least 10 characters.
The system must automatically set:
user_id = logged-in employee
status = pending

The employee must NOT be allowed to send:

{
    "user_id": 10,
    "status": "approved"
}
    /////////////////////////answer/////////////////////////////////////////
    public function store(Request $request)
{
    // 1. Get logged-in user
    $user = auth('api')->user();

    // 2. Check role
    if ($user->role !== 'employee') {
        return response()->json([
            'success' => false,
            'message' => 'Only employees can submit service requests',
        ], 403);
    }

    // 3. Check active account
    if (!$user->is_active) {
        return response()->json([
            'success' => false,
            'message' => 'Your account is inactive',
        ], 403);
    }

    // 4. Validate request data
    $validated = $request->validate([
        'asset_id' => 'required|exists:assets,id',
        'description' => 'required|string|min:10',
    ]);

    // 5. Find asset
    $asset = Asset::findOrFail($validated['asset_id']);

    // 6. Check department
    if ($asset->department_id !== $user->department_id) {
        return response()->json([
            'success' => false,
            'message' => 'You can only submit requests for assets in your department',
        ], 403);
    }

    // 7. Check asset status
    if ($asset->status !== 'assigned') {
        return response()->json([
            'success' => false,
            'message' => 'Service request can only be submitted for assigned assets',
        ], 400);
    }

    // 8. Check for existing pending request
    $existingRequest = ServiceRequest::where('asset_id', $asset->id)
        ->where('status', 'pending')
        ->exists();

    if ($existingRequest) {
        return response()->json([
            'success' => false,
            'message' => 'This asset already has a pending service request',
        ], 409);
    }

    // 9. Create service request
    $serviceRequest = ServiceRequest::create([
        'user_id' => $user->id,
        'asset_id' => $asset->id,
        'description' => $validated['description'],
        'status' => 'pending',
    ]);

    // 10. Return response
    return response()->json([
        'success' => true,
        'message' => 'Service request submitted successfully',
        'data' => $serviceRequest,
    ], 201);
}

*/
}
