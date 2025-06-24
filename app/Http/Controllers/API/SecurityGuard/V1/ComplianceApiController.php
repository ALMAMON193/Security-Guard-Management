<?php

namespace App\Http\Controllers\API\SecurityGuard\V1;

use Exception;
use App\Helpers\Helper;
use App\Models\Document;
use App\Models\Compliance;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Models\DocumentStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;



class ComplianceApiController extends Controller
{
    use ApiResponse;

    //fetch validation document
    public function complianceDocuments()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return $this->sendError('Unauthorized', ['error' => 'Please login first'], 401);
        }

        try {
            // Fetch document
            $document = Document::where('user_id', auth()->id())
                ->first();

            if (!$document) {
                return $this->sendResponse('No compliance document found', ['success' => 'No compliance documents found']);
            }

            // Prepare response data
            $responseData = [
                'id_copy' => [
                    'path' => $document->id_copy ? asset($document->id_copy) : '',
                    'status' => $document->id_copy ? $document->id_status : 'Not Uploaded'
                ],
                'psira_certificate' => [
                    'path' => $document->psira_certificate ? asset($document->psira_certificate) : '',
                    'status' => $document->psira_certificate ? $document->psira_status : 'Not Uploaded'
                ],
                'firearm_competency' => [
                    'path' => $document->firearm_competency ? asset($document->firearm_competency) : '',
                    'status' => $document->firearm_competency ? $document->firearm_status : 'Not Uploaded'
                ],
                'statement_of_results' => [
                    'path' => $document->statement_of_results ? asset($document->statement_of_results) : '',
                    'status' => $document->statement_of_results ? $document->statement_status : 'Not Uploaded'
                ],
            ];

            return $this->sendResponse($responseData, 'Compliance documents retrieved successfully');
        } catch (Exception $e) {
            Log::error('Error retrieving compliance documents: ' . $e->getMessage());
            return $this->sendError('Error retrieving compliance documents', ['error' => $e->getMessage()], 500);
        }
    }

    //store the compliance documents
    public function complianceDocumentUpload(Request $request)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return $this->sendError('Unauthorized', ['error' => 'Please login first'], 401);
        }

        try {
            // Validate uploaded files
            $validated = $request->validate([
                'id_copy' => ['nullable', 'file', 'mimes:pdf,jpg,png', 'max:2048'],
                'psira_certificate' => ['nullable', 'file', 'mimes:pdf,jpg,png', 'max:2048'],
                'firearm_competency' => ['nullable', 'file', 'mimes:pdf,jpg,svg,png', 'max:2048'],
                'statement_of_results' => ['nullable', 'file', 'mimes:pdf,jpg,png', 'max:2048'],
            ]);

            if (!$validated) {
                return $this->sendError('Validation Error', ['error' => 'Please upload valid file'], 401);
            }

            // Get or initialize document for the user
            $document = Document::firstOrNew(['user_id' => Auth::id()]);

            // Prepare document data
            $data = ['user_id' => Auth::id()];
            $fields = [
                'id_copy',
                'psira_certificate',
                'firearm_competency',
                'statement_of_results',
            ];

            foreach ($fields as $field) {
                if ($request->hasFile($field)) {
                    // Delete old file if exists
                    if ($document->$field) {
                        Helper::fileDelete($document->$field);
                    }
                    // Store new file and reset status to pending
                    $data[$field] = Helper::fileUpload($request->file($field), 'credentials');
                    $data[$field . '_status'] = 'pending'; // Reset status when new file is uploaded
                } else {
                    // Keep existing file if no new upload
                    $data[$field] = $document->$field;
                }
            }

            // Update or create the document record
            $document->fill($data)->save();

            // Prepare response with all fields
            $responseData = [
                'id_copy' => [
                    'path' => $document->id_copy ? asset($document->id_copy) : "",
                    'status' => $document->id_copy ? $document->id_status : 'Not Upload'
                ],
                'psira_certificate' => [
                    'path' => $document->psira_certificate ? asset($document->psira_certificate) : "",
                    'status' => $document->psira_certificate ? $document->psira_status : 'Not Upload'
                ],
                'firearm_competency' => [
                    'path' => $document->firearm_competency ? asset($document->firearm_competency) : "",
                    'status' => $document->firearm_competency ? $document->firearm_status : 'Not Upload'
                ],
                'statement_of_results' => [
                    'path' => $document->statement_of_results ? asset($document->statement_of_results) : "",
                    'status' => $document->statement_of_results ? $document->statement_status : 'Not Upload'
                ]
            ];

            return $this->sendResponse($responseData, 'Compliance documents stored successfully');
        } catch (Exception $e) {
            Log::error('Error storing compliance documents: ' . $e->getMessage());
            return $this->sendError('Server Error', ['error' => 'Failed to store compliance documents'], 500);
        }
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
                    'psira_status' => 'pending'
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
