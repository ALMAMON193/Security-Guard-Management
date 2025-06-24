<?php

namespace App\Http\Controllers\API\SecurityGuard\V1;

use Exception;
use App\Helpers\Helper;
use App\Models\Document;
use App\Models\Compliance;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;



class ComplianceApiController extends Controller
{
    use ApiResponse;

   public function __construct()
    {
        $this->middleware(['auth:sanctum', 'security_guard']);
    }

    public function createCompliance(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'psira_certificate' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:20048', // Max size in KB (20MB)
            'service_offered' => 'required|array',
            'service_offered.*' => 'required|string|max:255',
            'grade_of_guard' => 'required|string|max:255',
        ]);

        // Check if user is authenticated
        if (!auth()->check()) {
            return $this->sendError('Unauthorized', ['error' => 'Please login first'], 401);
        }
        $user = auth()->user();
        try {
            // Handle file upload for psira_certificate
            $filePath = null;
            $document = Document::where('user_id', $user->id)->first();

            if ($request->hasFile('psira_certificate')) {

                // Delete old file if it exists
                if ($document && $document->psira_certificate) {
                    $oldFilePath = public_path($document->psira_certificate);
                    if (file_exists($oldFilePath)) {
                        Helper::fileDelete($oldFilePath);
                    }
                }
                // Upload new file using Helper (assumes it returns relative path)
                $filePath = Helper::fileUpload($request->file('psira_certificate'), 'documents');
            }

            // Prepare attributes for Compliance model
            $attributes = [
                'service_offered' => json_encode($validatedData['service_offered']),
                'grade_of_guard' => $validatedData['grade_of_guard'],
                'user_id' => $user->id,
            ];

            // Update or create compliance record
            $compliance = Compliance::updateOrCreate(
                ['user_id' => $user->id],
                $attributes
            );

            // Update or create document record
            $document = Document::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'psira_certificate' => $filePath ?? ($document->psira_certificate ?? null),
                ]
            );

            // Mark user as compliant
            $user->is_compliance = true;
            $user->save();

            // Determine success message
            $message = $compliance->wasRecentlyCreated
                ? 'Compliance Setup created successfully.'
                : 'Compliance Setup updated successfully.';

            // Prepare response data
            $responseData = [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'psira_certificate' => $document->psira_certificate ? asset($document->psira_certificate) : 'N/A',
                'service_offered' => $validatedData['service_offered'],
                'grade_of_guard' => $validatedData['grade_of_guard'],
            ];

            return $this->sendResponse($responseData, $message);
        } catch (Exception $e) {
            Log::error('CreateComplianceSetup Error', ['error' => $e->getMessage()]);
            return $this->sendError('Error creating Compliance Setup', ['error' => $e->getMessage()], 500);
        }
    }
}
