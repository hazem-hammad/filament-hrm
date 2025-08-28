<?php

namespace Database\Seeders;

use App\Models\VacationType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VacationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeds Egyptian Labor Law vacation types based on Law No. 12 of 2003.
     */
    public function run(): void
    {
        $vacationTypes = [
            [
                'name' => 'Annual Leave',
                'balance' => 21,
                'unlock_after_months' => 12,
                'required_days_before' => 15,
                'requires_approval' => true,
                'status' => true,
                'description' => 'Annual paid vacation as per Egyptian Labor Law Article 43. Employee is entitled to 21 days after completing one year of service. Cannot be split into periods of less than 7 consecutive days except with employee consent.',
            ],
            [
                'name' => 'Sick Leave',
                'balance' => 180,
                'unlock_after_months' => 0,
                'required_days_before' => 0,
                'requires_approval' => false,
                'status' => true,
                'description' => 'Medical leave as per Egyptian Labor Law Article 44. Employee is entitled to sick leave of up to 180 days per year. First 30 days with full pay, next 30 days with 75% pay, and remaining days without pay. Medical certificate required.',
            ],
            [
                'name' => 'Emergency Leave',
                'balance' => 7,
                'unlock_after_months' => 0,
                'required_days_before' => 0,
                'requires_approval' => true,
                'status' => true,
                'description' => 'Emergency leave for exceptional circumstances. Employee may be granted up to 7 days per year for urgent personal matters. Requires manager approval and valid justification.',
            ],
            [
                'name' => 'Maternity Leave',
                'balance' => 90,
                'unlock_after_months' => 0,
                'required_days_before' => 30,
                'requires_approval' => false,
                'status' => true,
                'description' => 'Maternity leave as per Egyptian Labor Law Article 46. Female employee is entitled to 90 days maternity leave with full pay. Can be extended for additional periods without pay upon medical recommendation.',
            ],
            [
                'name' => 'Study Leave',
                'balance' => 15,
                'unlock_after_months' => 6,
                'required_days_before' => 30,
                'requires_approval' => true,
                'status' => true,
                'description' => 'Educational leave for employees pursuing studies. Up to 15 days per year for attending exams or educational programs. Requires manager approval and proof of enrollment.',
            ],
            [
                'name' => 'Bereavement Leave',
                'balance' => 5,
                'unlock_after_months' => 0,
                'required_days_before' => 0,
                'requires_approval' => false,
                'status' => true,
                'description' => 'Compassionate leave for death of immediate family members. Employee is entitled to 5 days paid leave. Additional unpaid leave may be granted upon request.',
            ],
            [
                'name' => 'Marriage Leave',
                'balance' => 7,
                'unlock_after_months' => 0,
                'required_days_before' => 15,
                'requires_approval' => true,
                'status' => true,
                'description' => 'Special leave for employee marriage. Employee is entitled to 7 days paid leave upon marriage. Requires 15 days advance notice and marriage certificate.',
            ],
            [
                'name' => 'Pilgrimage Leave (Hajj)',
                'balance' => 30,
                'unlock_after_months' => 12,
                'required_days_before' => 60,
                'requires_approval' => true,
                'status' => true,
                'description' => 'Religious leave for Hajj pilgrimage. Employee may be granted up to 30 days leave once during employment. Requires advance notice and religious authority confirmation.',
            ],
            [
                'name' => 'Military Service Leave',
                'balance' => 365,
                'unlock_after_months' => 0,
                'required_days_before' => 30,
                'requires_approval' => false,
                'status' => true,
                'description' => 'Military service leave as required by Egyptian law. Employee position and benefits are protected during mandatory military service. Requires military service documentation.',
            ],
            [
                'name' => 'Paternity Leave',
                'balance' => 3,
                'unlock_after_months' => 0,
                'required_days_before' => 7,
                'requires_approval' => true,
                'status' => true,
                'description' => 'Paternity leave for new fathers. Employee is entitled to 3 days paid leave upon birth of child. Requires birth certificate and advance notification.',
            ],
        ];

        foreach ($vacationTypes as $vacationType) {
            VacationType::firstOrCreate(
                ['name' => $vacationType['name']],
                $vacationType
            );
        }

        $this->command->info('Egyptian Labor Law vacation types have been seeded successfully.');
    }
}
