<?php

namespace App\Http\Controllers\API\BusinessOwner\V1;

use Exception;
use App\Helpers\Helper;
use App\Models\Document;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Models\CompanyProfile;
use App\Models\ComponyProfile;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CompanyProfileController extends Controller
{
    use ApiResponse;

    public function CreateCompanyProfile(Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'business_name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'area_of_operation' => 'required|string|max:255',
            'service_offered' => 'required|array',
            'service_offered.*' => 'required|string|max:255',
            'enable_statutory_deductions' => 'required|boolean',
            'coida_certificate' => 'nullable|mimes:jpeg,png,jpg,gif,pdf,svg|max:20048',
            'uif_certificate' => 'nullable|mimes:jpeg,png,jpg,gif,pdf,svg|max:20048',
            'psira_certificate' => 'nullable|mimes:jpeg,png,jpg,gif,pdf,svg|max:20048',
        ]);

        // Validation check
        if ($validator->fails()) {
            return $this->sendError('Validation failed', $validator->errors()->toArray(), 422);
        }

        // Check if user is authenticated
        if (!auth()->check()) {
            return $this->sendError('Unauthorized', ['error' => 'First Login'], 401);
        }

        $user = auth()->user();
        $validatedData = $validator->validated();

        try {
            // Get existing records
            $companyProfile = ComponyProfile::where('user_id', $user->id)->first();
            $document = Document::where('user_id', $user->id)->first();

            // Handle file uploads
            $files = [
                'coida_certificate' => null,
                'uif_certificate' => null,
                'psira_certificate' => null,
            ];

            foreach (['coida_certificate', 'uif_certificate', 'psira_certificate'] as $certificate) {
                if ($request->hasFile($certificate)) {
                    // Delete old file if exists
                    if ($document && $document->$certificate) {
                        $oldFilePath = public_path($document->$certificate);
                        if (file_exists($oldFilePath)) {
                            Helper::fileDelete($oldFilePath);
                        }
                    }
                    // Upload new file
                    $files[$certificate] = Helper::fileUpload($request->file($certificate), 'documents');
                }
            }

            // Prepare attributes for CompanyProfile
            $profileAttributes = [
                'business_name' => $validatedData['business_name'],
                'owner_name' => $validatedData['owner_name'],
                'area_of_operation' => $validatedData['area_of_operation'],
                'enable_statutory_deductions' => $validatedData['enable_statutory_deductions'],
                'service_offered' => json_encode($validatedData['service_offered']),
            ];

            // Update or create CompanyProfile record
            $companyProfile = ComponyProfile::updateOrCreate(
                ['user_id' => $user->id],
                $profileAttributes
            );

            // Prepare attributes for Document
            $documentAttributes = [
                'coida_certificate' => $files['coida_certificate'] ?? ($document->coida_certificate ?? null),
                'uif_certificate' => $files['uif_certificate'] ?? ($document->uif_certificate ?? null),
                'psira_certificate' => $files['psira_certificate'] ?? ($document->psira_certificate ?? null),
            ];

            // Update or create Document record
            $document = Document::updateOrCreate(
                ['user_id' => $user->id],
                $documentAttributes
            );
            $user->save();

            // Prepare response message
            $message = $companyProfile->wasRecentlyCreated
                ? 'Company Profile created successfully.'
                : 'Company Profile updated successfully.';

            // Prepare response data
            $responseData = [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'business_name' => $companyProfile->business_name,
                'owner_name' => $companyProfile->owner_name,
                'area_of_operation' => $companyProfile->area_of_operation,
                'enable_statutory_deductions' => $companyProfile->enable_statutory_deductions,
                'service_offered' => json_decode($companyProfile->service_offered, true),
                'coida_certificate' => $document->coida_certificate ? asset($document->coida_certificate) : "N/A",
                'uif_certificate' => $document->uif_certificate ? asset($document->uif_certificate) : "N/A",
                'psira_certificate' => $document->psira_certificate ? asset($document->psira_certificate) : "N/A",
            ];

            return $this->sendResponse($responseData, $message);
        } catch (Exception $e) {
            Log::error('CreateCompanyProfile Error', ['error' => $e->getMessage()]);
            return $this->sendError('Error creating Company Profile', ['error' => $e->getMessage()], 500);
        }
    }
}
