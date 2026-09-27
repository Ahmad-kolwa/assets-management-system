<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\User;
use App\Models\ServiceRequest;

class HodController extends Controller
{
    // hod/admin View assets in HOD's department
    public function assets(Request $request)
    {
        $departmentId = $request->user()->department_id;

        $assets = Asset::with(['category', 'department'])
            ->where('department_id', $departmentId)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $assets
        ]);
    }

    // View staff in HOD's department
    public function staff(Request $request)
    {
        $departmentId = $request->user()->department_id;

        $staff = User::where('department_id', $departmentId)
            ->select('id', 'name', 'email', 'role', 'is_active')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $staff
        ]);
    }

    // View service requests from HOD's department
    public function requests(Request $request)
    {
        $departmentId = $request->user()->department_id;

        $requests = ServiceRequest::with(['user', 'asset'])
            ->whereHas('asset', function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })
            ->get();

        return response()->json([
            'success' => true,
            'data' => $requests
        ]);
    }

    // Department report
    public function reports(Request $request)
    {
        $departmentId = $request->user()->department_id;

        $report = [
            'department_id' => $departmentId,

            'assets' => [
                'total' => Asset::where('department_id', $departmentId)->count(),
                'available' => Asset::where('department_id', $departmentId)
                    ->where('status', 'available')
                    ->count(),
                'assigned' => Asset::where('department_id', $departmentId)
                    ->where('status', 'assigned')
                    ->count(),
                'maintenance' => Asset::where('department_id', $departmentId)
                    ->where('status', 'maintenance')
                    ->count(),
            ],

            'staff' => User::where('department_id', $departmentId)->count(),

            'service_requests' => [
                'total' => ServiceRequest::whereHas('asset', function ($q) use ($departmentId) {
                    $q->where('department_id', $departmentId);
                })->count(),

                'pending' => ServiceRequest::whereHas('asset', function ($q) use ($departmentId) {
                    $q->where('department_id', $departmentId);
                })->where('status', 'pending')->count(),

                'approved' => ServiceRequest::whereHas('asset', function ($q) use ($departmentId) {
                    $q->where('department_id', $departmentId);
                })->where('status', 'approved')->count(),

                'rejected' => ServiceRequest::whereHas('asset', function ($q) use ($departmentId) {
                    $q->where('department_id', $departmentId);
                })->where('status', 'rejected')->count(),

                'completed' => ServiceRequest::whereHas('asset', function ($q) use ($departmentId) {
                    $q->where('department_id', $departmentId);
                })->where('status', 'completed')->count(),
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }
}
