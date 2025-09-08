<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Notifications\EmployeeWelcomeNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    /**
     * Send welcome emails to all employees who have email addresses
     */
    public function sendWelcomeEmails(Request $request): JsonResponse
    {
        try {
            // Get all active employees with email addresses and eager load relationships
            $employees = Employee::query()
                ->with(['department', 'position', 'manager'])
                ->whereNotNull('email')
                ->where('email', '!=', '')
                ->get();

            if ($employees->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No employees with email addresses found.'
                ], 404);
            }

            $sentCount = 0;
            $failedCount = 0;
            $errors = [];

            foreach ($employees as $employee) {
                try {
                    // Generate a temporary password for the welcome email
                    $temporaryPassword = Str::password(12, true, true, true, false);
                    $passwordSetupToken = Str::random(64);

                    // Update employee's password in database
                    $employee->update([
                        'password' => Hash::make($temporaryPassword)
                    ]);

                    // Send welcome email
                    Notification::sendNow($employee, new EmployeeWelcomeNotification($temporaryPassword, $passwordSetupToken));

                    $sentCount++;

                    Log::info('Bulk welcome email sent', [
                        'employee_id' => $employee->employee_id,
                        'email' => $employee->email,
                        'admin_initiated' => true
                    ]);
                } catch (\Exception $e) {
                    $failedCount++;
                    $errorMessage = "Failed to send email to {$employee->email}: " . $e->getMessage();
                    $errors[] = $errorMessage;

                    Log::error('Failed to send bulk welcome email', [
                        'employee_id' => $employee->employee_id,
                        'email' => $employee->email,
                        'error' => $e->getMessage()
                    ]);
                }
                // } catch (\Exception $e) {
                //     $failedCount++;
                //     $errorMessage = "Failed to send email to {$employee->email}: " . $e->getMessage();
                //     $errors[] = $errorMessage;

                //     Log::error('Failed to send bulk welcome email', [
                //         'employee_id' => $employee->employee_id,
                //         'email' => $employee->email,
                //         'error' => $e->getMessage()
                //     ]);
                // }
            }

            $message = "Welcome emails processed: {$sentCount} sent successfully";
            if ($failedCount > 0) {
                $message .= ", {$failedCount} failed";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'total_employees' => $employees->count(),
                    'sent_count' => $sentCount,
                    'failed_count' => $failedCount,
                    'errors' => $errors
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Bulk welcome email process failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to process welcome emails: ' . $e->getMessage()
            ], 500);
        }
    }
}
