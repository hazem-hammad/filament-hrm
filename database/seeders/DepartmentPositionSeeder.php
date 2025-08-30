<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DepartmentPositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['name' => 'Human Resources', 'status' => true],
            ['name' => 'Information Technology', 'status' => true],
            ['name' => 'Finance', 'status' => true],
            ['name' => 'Marketing', 'status' => true],
        ];

        foreach ($departments as $dept) {
            $department = \App\Models\Department::create($dept);
            
            switch ($department->name) {
                case 'Human Resources':
                    $positions = ['HR Manager', 'HR Specialist', 'Recruiter'];
                    break;
                case 'Information Technology':
                    $positions = ['Software Developer', 'System Administrator', 'QA Engineer'];
                    break;
                case 'Finance':
                    $positions = ['Financial Analyst', 'Accountant', 'Finance Manager'];
                    break;
                case 'Marketing':
                    $positions = ['Marketing Manager', 'Social Media Specialist', 'Content Creator'];
                    break;
                default:
                    $positions = [];
            }
            
            foreach ($positions as $position) {
                \App\Models\Position::create([
                    'name' => $position,
                    'status' => true,
                    'department_id' => $department->id,
                ]);
            }
        }

    }
}
