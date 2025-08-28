<?php

namespace Database\Seeders;

use App\Models\AttendanceType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttendanceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeds Egyptian Labor Law attendance types based on Law No. 12 of 2003.
     */
    public function run(): void
    {
        $attendanceTypes = [
            [
                'name' => 'Overtime',
                'has_limit' => true,
                'max_hours_per_month' => 48,
                'max_requests_per_month' => 20,
                'max_hours_per_request' => 4.0,
                'requires_approval' => true,
                'status' => true,
                'description' => 'Overtime work as per Egyptian Labor Law Article 85. Maximum 2 hours per day, not exceeding 48 hours per month. Paid at 150% of regular wage rate. Requires manager approval.',
            ],
            [
                'name' => 'Late Arrival',
                'has_limit' => true,
                'max_hours_per_month' => 8,
                'max_requests_per_month' => 10,
                'max_hours_per_request' => 2.0,
                'requires_approval' => false,
                'status' => true,
                'description' => 'Late arrival tracking for employees. Maximum 2 hours per instance, up to 8 hours per month. Automatic deduction from salary after grace period.',
            ],
            [
                'name' => 'Early Leave',
                'has_limit' => true,
                'max_hours_per_month' => 16,
                'max_requests_per_month' => 12,
                'max_hours_per_request' => 4.0,
                'requires_approval' => true,
                'status' => true,
                'description' => 'Early departure from work. Maximum 4 hours per request, up to 16 hours per month. Requires supervisor approval and salary deduction applies.',
            ],
            [
                'name' => 'Night Shift',
                'has_limit' => false,
                'max_hours_per_month' => null,
                'max_requests_per_month' => null,
                'max_hours_per_request' => null,
                'requires_approval' => true,
                'status' => true,
                'description' => 'Night shift work as per Egyptian Labor Law Article 86. Work between 10 PM and 6 AM. Paid at 125% of regular wage rate. Requires management approval.',
            ],
            [
                'name' => 'Holiday Work',
                'has_limit' => true,
                'max_hours_per_month' => 24,
                'max_requests_per_month' => 6,
                'max_hours_per_request' => 8.0,
                'requires_approval' => true,
                'status' => true,
                'description' => 'Work on official holidays as per Egyptian Labor Law Article 87. Maximum 8 hours per holiday, up to 24 hours per month. Paid at 200% of regular wage rate.',
            ],
            [
                'name' => 'Weekend Work',
                'has_limit' => true,
                'max_hours_per_month' => 32,
                'max_requests_per_month' => 8,
                'max_hours_per_request' => 8.0,
                'requires_approval' => true,
                'status' => true,
                'description' => 'Work on weekends (Friday/Saturday). Maximum 8 hours per day, up to 32 hours per month. Requires management approval and compensatory time off.',
            ],
            [
                'name' => 'Remote Work',
                'has_limit' => false,
                'max_hours_per_month' => null,
                'max_requests_per_month' => null,
                'max_hours_per_request' => null,
                'requires_approval' => true,
                'status' => true,
                'description' => 'Remote/home-based work arrangement. Flexible hours within agreed framework. Requires manager approval and adherence to productivity metrics.',
            ],
            [
                'name' => 'Shift Exchange',
                'has_limit' => true,
                'max_hours_per_month' => null,
                'max_requests_per_month' => 4,
                'max_hours_per_request' => 8.0,
                'requires_approval' => true,
                'status' => true,
                'description' => 'Exchange of work shifts between employees. Maximum 4 exchanges per month. Requires approval from both employees and supervisor.',
            ],
            [
                'name' => 'Training Hours',
                'has_limit' => false,
                'max_hours_per_month' => null,
                'max_requests_per_month' => null,
                'max_hours_per_request' => null,
                'requires_approval' => false,
                'status' => true,
                'description' => 'Time spent in company training programs, seminars, or professional development. Considered part of regular work hours. No specific limits.',
            ],
            [
                'name' => 'Compensatory Time',
                'has_limit' => true,
                'max_hours_per_month' => 40,
                'max_requests_per_month' => 10,
                'max_hours_per_request' => 8.0,
                'requires_approval' => true,
                'status' => true,
                'description' => 'Time off in lieu of overtime payment. Can accumulate up to 40 hours per month. Requires manager approval and must be used within 3 months.',
            ],
        ];

        foreach ($attendanceTypes as $attendanceType) {
            AttendanceType::firstOrCreate(
                ['name' => $attendanceType['name']],
                $attendanceType
            );
        }

        $this->command->info('Egyptian Labor Law attendance types have been seeded successfully.');
    }
}
