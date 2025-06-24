<?php

namespace App\Http\Controllers\API\BusinessOwner\V1;

use Exception;
use App\Helpers\Helper;
use App\Models\AddGuard;
use App\Traits\ApiResponse;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class GuardApiController extends Controller
{
    use ApiResponse;

    // add a new Guard
    public function createGuard(Request $request)
    {

        $user = Auth::user();
        if (!$user) {
            return $this->sendError('Unauthorized', ['error' => 'User is not logged in'], 401);
        }
        if ($user->user_type == 'business_owner') {
            return $this->sendError('Unauthorized', ['error' => 'User is not a business owner'], 401);
        }
        // Validate the incoming request data
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'psira_number' => 'required|string',
            'certificate_file' => 'required|file|mimes:pdf,jpg,png,jpeg,svg,webp|max:20048',
            'wage_rate' => 'required|numeric|min:0',
            'rate_type' => 'required|in:hourly,daily,monthly,yearly',
            'area_of_operation' => 'required|string|max:255',
            'controller_assignment' => 'required|string|max:255',
        ]);

        try {
            // Handle file upload for certificate
            if ($request->hasFile('certificate_file')) {
                $file = $request->file('certificate_file');
                $filePath = Helper::fileUpload($file, 'guard_certificates');
            }

            // Create new guard record
            $guard = AddGuard::create([
                'full_name' => $validated['full_name'],
                'psira_number' => $validated['psira_number'],
                'certificate_file' => $filePath,
                'wage_rate' => $validated['wage_rate'],
                'rate_type' => $validated['rate_type'],
                'area_of_operation' => $validated['area_of_operation'],
                'controller_assignment' => $validated['controller_assignment']
            ]);
            $responseBody = [
                'guard_id' => $guard->id,
                'full_name' => $guard->full_name,
                'psira_number' => $guard->psira_number,
                'certificate_file' => asset($guard->certificate_file),
                'wage_rate' => intval($guard->wage_rate),
                'rate_type' => $guard->rate_type,
                'area_of_operation' => $guard->area_of_operation,
                'controller_assignment' => $guard->controller_assignment
            ];
            return $this->sendResponse($responseBody, 'Guard created successfully');
        } catch (Exception $e) {
            Log::error('Error creating guard: ' . $e->getMessage());
            return $this->sendError('Error creating guard', ['error' => $e->getMessage()], 500);
        }
    }
}
