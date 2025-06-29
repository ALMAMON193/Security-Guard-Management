<?php

namespace App\Http\Controllers\API\BusinessOwner\V1;

use Exception;
use App\Helpers\Helper;
use App\Models\Document;
use App\Models\Compliance;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class ComplianceSetupController extends Controller
{
    use ApiResponse;
    public function createComplianceSetup(Request $request)
    {
        $validatedData = $request->validate([
            'company_location' => 'nullable|string|max:255',
            'psira_certificate' => 'nullable|mimes:jpeg,png,jpg,pdf|max:200048',
            'enable_statutory_deductions' => 'nullable|boolean',
        ]);
        if (!auth()->check()) {
            return $this->sendError('Unauthorized', ['error' => 'First Login'], 401);
        }
        $user = auth()->user();
        try {
            $compliance = Compliance::where('user_id', $user->id)->first();
            $document = Document::where('user_id', $user->id)->first();
            $file = null;

            if ($request->hasFile('psira_certificate')) {
                // Delete old file if exists
                if ($document && $document->psira_certificate) {
                    $oldFilePath = public_path($document->psira_certificate);
                    if (file_exists($oldFilePath)) {
                        Helper::fileDelete($oldFilePath);
                    }
                }
                // Upload new file (returns relative path like 'uploads/documents/abc.pdf')
                $file = Helper::fileUpload($request->file('psira_certificate'), 'documents');
            }

            // Update or create compliance record
            $attributes = [
                'company_location' => $validatedData['company_location'] ?? ($compliance->company_location ?? null),
                'enable_statutory_deductions' => isset($validatedData['enable_statutory_deductions']) ? ($validatedData['enable_statutory_deductions'] ? 1 : 0) : ($compliance->enable_statutory_deductions ?? 0),
            ];

            $compliance = Compliance::updateOrCreate(
                ['user_id' => $user->id],
                $attributes
            );

            // Update or create document record (store only relative file path)
            $document = Document::updateOrCreate(
                ['user_id' => $user->id],
                ['psira_certificate' => $file ?? ($document->psira_certificate ?? null)]
            );

            // Mark user as compliant
            $user->is_compliance = true;
            $user->save();

            $message = $compliance->wasRecentlyCreated
                ? 'Compliance Setup created successfully.'
                : 'Compliance Setup updated successfully.';

            // Prepare response data
            $responseData = [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'company_location' => $compliance->company_location,
                'enable_statutory_deductions' => $compliance->enable_statutory_deductions,
                'psira_certificate' => $document->psira_certificate ? asset($document->psira_certificate) : "N/A",
            ];

            return $this->sendResponse($responseData, $message);
        } catch (Exception $e) {
            Log::error('CreateComplianceSetup Error', ['error' => $e->getMessage()]);
            return $this->sendError('Error creating Compliance Setup', ['error' => $e->getMessage()], 500);
        }
    }
}
