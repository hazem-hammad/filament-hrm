<?php

namespace App\Http\Controllers\Api\V1\Employee;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class AttendanceController extends Controller
{
    public function getDuration(): JsonResponse
    {
        $attendance = Attendance::where('employee_id', auth()->id())
            ->whereDate('date', today())
            ->whereNull('check_out_time')
            ->first();

        if (!$attendance || !$attendance->check_in_time) {
            return response()->json(['duration' => '00:00:00']);
        }

        $checkIn = Carbon::parse($attendance->check_in_time);
        $now = now();
        $duration = $checkIn->diff($now);

        $formattedDuration = sprintf('%02d:%02d:%02d', 
            $duration->h + ($duration->days * 24), 
            $duration->i, 
            $duration->s
        );

        return response()->json(['duration' => $formattedDuration]);
    }
}