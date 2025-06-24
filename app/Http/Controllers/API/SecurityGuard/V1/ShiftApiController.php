<?php

namespace App\Http\Controllers\API\SecurityGuard\V1;

use Exception;
use Carbon\Carbon;
use App\Models\Shift;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class ShiftApiController extends Controller
{
    use ApiResponse;


    public function createShift(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date_format:Y-m-d',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'shift_schedule' => 'required|string|max:255',
            'status' => 'required|string|max:255',
        ]);

        try {
            $shift = Shift::create([
                'user_id' => auth()->id(),
                'date' => $validated['date'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'shift_schedule' => $validated['shift_schedule'],
                'status' => $validated['status'] ?? 'active',
            ]);

            $responseBody = [
                'shift_id' => $shift->id,
                'user_id' => $shift->user_id,
                'date' => $shift->date,
                'start_time' => $shift->start_time,
                'end_time' => $shift->end_time,
                'shift_schedule' => $shift->shift_schedule,
                'status' => $shift->status,
                'created_at' => $shift->created_at,
            ];

            $message = 'Shift created successfully';

            return $this->sendResponse($responseBody, $message);
        } catch (Exception $e) {
            Log::error('CreateShift Error', ['error' => $e->getMessage()]);
            return $this->sendError('Error creating shift', ['error' => $e->getMessage()], 500);
        }
    }

    public function todayShift()
    {
        try {
            $shifts = Shift::whereDate('date', Carbon::today())->with('user')->get();

            $responseData = $shifts->map(function ($shift) {
                return [
                    'id' => $shift->id,
                    'user_id' => $shift->user_id,
                    'user_name' => $shift->user?->name ?? 'Unknown',
                    'avatar' => 'No Image',
                    'date' => $shift->date,
                    'start_time' => $shift->start_time,
                    'end_time' => $shift->end_time,
                    'shift_schedule' => $shift->shift_schedule,
                    'status' => $shift->status,
                    'created_at' => $shift->created_at,
                ];
            });

            $message = 'Todays shifts retrieved successfully';
            return $this->sendResponse($responseData, $message);
        } catch (Exception $e) {
            Log::error('TodayShift Error', ['error' => $e->getMessage()]);
            return $this->sendError('Error retrieving shifts', ['error' => $e->getMessage()], 500);
        }
    }
    //today shift delete
    public function todayShiftDelete($id)
    {
        try {
            $shift = Shift::where('id', $id);
            //selected shift id
            if (!$shift->exists()) {
                return $this->sendError('Shift not found', ['error' => 'Shift not found'], 404);
            }

            $shift->delete();
            $message = 'Shift deleted successfully';
            return $this->sendResponse([], $message);
        } catch (Exception $e) {

            $shift->delete();
            $message = 'Shift deleted successfully';
            return $this->sendResponse([], $message);
        } catch (Exception $e) {
            Log::error('TodayShiftDelete Error', ['error' => $e->getMessage()]);
            return $this->sendError('Error deleting shift', ['error' => $e->getMessage()], 500);
        }
    }
}
