<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Document;
use Exception;
use App\Helpers\Helper;
use App\Models\Compliance;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class ComplianceSetupController extends Controller
{
    use ResponseTrait;

    public function CreateComplianceSetup(Request $request)
    {
        $validatedData = $request->validate([
            'company_location' => 'nullable|string|max:255',
            'psira_certificate' => 'nullable|mimes:jpeg,png,jpg,pdf|max:20048',
            'enable_statutory_deductions' => 'nullable|boolean',
        ]);

        if (!auth()->check()) {
            return $this->sendError('Please login first', [], 422);
        }

        $user = auth()->user();

        try {
            $compliance = Compliance::where('user_id', $user->id)->first();
            $document = Document::where('user_id', $user->id)->first();

            $file = null;

            if ($request->hasFile('psira_certificate')) {
                // 🧹 Delete old file if exists
                if ($document && $document->psira_certificate) {
                    $oldFilePath = public_path($document->psira_certificate);
                    if (file_exists($oldFilePath)) {
                        Helper::fileDelete($oldFilePath);
                    }
                }

                // 📁 Upload new file (returns relative path like 'uploads/documents/abc.pdf')
                $file = Helper::fileUpload($request->file('psira_certificate'), 'documents');
            }

            // ✅ Update or create compliance record
            $attributes = [
                'company_location' => $validatedData['company_location'] ?? ($compliance->company_location ?? null),
                'enable_statutory_deductions' => $validatedData['enable_statutory_deductions'] ? 1 : 0,
            ];

            $compliance = Compliance::updateOrCreate(
                ['user_id' => $user->id],
                $attributes
            );

            // ✅ Update or create document record (store only relative file path)
            $document = Document::updateOrCreate(
                ['user_id' => $user->id],
                ['psira_certificate' => $file ?? ($document->psira_certificate ?? null)]
            );

            // ✅ Mark user as compliant
            $user->is_compliance = true;
            $user->save();

            $message = $compliance->wasRecentlyCreated
                ? 'Compliance Setup created successfully.'
                : 'Compliance Setup updated successfully.';

            // ✅ Prepare response data
            $responseData = [
                'user' => $user,
                'compliance' => $compliance,
                'documents' => [
                    'psira_certificate_url' => $document->psira_certificate ? asset($document->psira_certificate) : null
                ]
            ];

            return $this->sendResponse(data: $responseData, message: $message);
        } catch (Exception $e) {
            Log::error('CreateComplianceSetup Error', ['error' => $e->getMessage()]);
            return $this->sendError('Something went wrong. Please try again later.', 500);
        }
    }





}
