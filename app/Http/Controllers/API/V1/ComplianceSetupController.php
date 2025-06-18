<?php

namespace App\Http\Controllers\API\V1;

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
            'user_id' => 'required|exists:users,id',
            'compony_location' => 'nullable|string|max:255',
            'psira_certificate' => 'nullable|in:yes,no',
            'psira_certificate_path' => 'nullable|image|mimes:jpeg,png,jpg|max:20048',
            'enable_statutory_deductions' => 'nullable|boolean',
        ]);

        try {
            $file = $request->file('psira_certificate_path')
                ? Helper::fileUpload($request->file('psira_certificate_path'), 'compliances')
                : null;

            $attributes = array_merge($validatedData, [
                'psira_certificate_path' => $file ? asset($file) : null,
                'enable_statutory_deductions' => $validatedData['enable_statutory_deductions'] ? 1 : 0,
            ]);

            $compliance = Compliance::updateOrCreate(
                ['user_id' => $validatedData['user_id']],
                $attributes
            );

            $user = $compliance->user;
            $user->is_compliance = true;
            $user->save();

            $message = $compliance->wasRecentlyCreated
                ? 'Compliance Setup created successfully.'
                : 'Compliance Setup updated successfully.';

            return $this->sendResponse(data: $compliance, message: $message);
        } catch (Exception $e) {
            Log::error('CreateComplianceSetup Error', ['error' => $e->getMessage()]);
            return $this->sendError('Something went wrong. Please try again later.', 500);
        }
    }
}
