
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Asset;
use Illuminate\Support\Facades\Log;
use App\Models\ServiceRequest;
use App\Models\User;


 class AssetController extends Controller
 {
//     /**
//      * Display a listing of the resource.
//      */
public function index()
{
    $assets = Asset::with(['category', 'department'])->get();

    return response()->json([
        'success' => true,
        'data' => $assets
    ]);
}

//     /**
//      * Store a newly created resource in storage.
//      */


// public function store(Request $request)
// {
//     $validated = $request->validate([
//         'category_id' => 'required|exists:categories,id',
//         'asset_name'  => 'required|string|max:255',
//         'asset_code'  => 'required|string|unique:assets,asset_code',
//         'status'      => 'nullable|in:available,assigned,maintenance',
//     ]);

//     try {
//         $asset = Asset::create($validated);

//         return response()->json([
//             'success' => true,
//             'message' => 'Asset created successfully.',
//             'data' => $asset,
//         ], 201);
//     } catch (\Exception $e) {
//         Log::error($e);
//         return response()->json([
//             'error' => $e->getMessage()
//         ], 500);
//     }
// }
       public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'department_id' => 'required|exists:departments,id',

            'asset_name'  => 'required|string|max:255',
            'asset_code'  => 'required|string|unique:assets,asset_code',
            'status'      => 'nullable|in:available,assigned,maintenance',
        ]);

        $asset = Asset::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Asset created successfully.',
            'data' => $asset,
        ], 201);

    }

//       public function store(Request $request)
// {
//     return response()->json([
//         'received' => $request->all()
//     ]);
// }
    /**
     * Display the specified resource.
     */
   public function show($id)
{
    $asset = Asset::with('category')->findOrFail($id);

    return response()->json([
        'success' => true,
        'data' => $asset
    ]);
}

    // /**
    //  * Update the specified resource in storage.
    //  */
   public function update(Request $request, $id)
{
    $asset = Asset::findOrFail($id);

    $validated = $request->validate([
        'category_id' => 'required|exists:categories,id',
                'department_id' => 'required|exists:departments,id',
        'asset_name'  => 'required|string|max:255',
        'asset_code'  => 'required|string|unique:assets,asset_code,' . $asset->id,
        'status'      => 'nullable|in:available,assigned,maintenance',
    ]);

    $asset->update($validated);

    return response()->json([
        'success' => true,
        'message' => 'Asset updated successfully.',
        'data' => $asset,
    ]);
}

    // /**
    //  * Remove the specified resource from storage.
    //  */
  public function destroy($id)
{
    $asset = Asset::findOrFail($id);

    $asset->delete();

    return response()->json([
        'success' => true,
        'message' => 'Asset deleted successfully.'
    ]);
}


public function reports()
{
    return response()->json([
        'assets' => [
            'total' => Asset::count(),
            'available' => Asset::where('status', 'available')->count(),
            'assigned' => Asset::where('status', 'assigned')->count(),
            'maintenance' => Asset::where('status', 'maintenance')->count(),
        ],

        'service_requests' => [
            'total' => ServiceRequest::count(),
            'pending' => ServiceRequest::where('status', 'pending')->count(),
            'approved' => ServiceRequest::where('status', 'approved')->count(),
            'rejected' => ServiceRequest::where('status', 'rejected')->count(),
            'completed' => ServiceRequest::where('status', 'completed')->count(),
        ],

        'users' => [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
        ]
    ]);
}
}
