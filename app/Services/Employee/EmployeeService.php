<?php

namespace App\Services\Employee;

use App\DTOs\V1\Employee\RegisterEmployeeDTO;
use App\Repositories\Interfaces\EmployeeRepositoryInterface;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;
use App\Enum\EmployeeLevel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Exception;

class EmployeeService
{
    public function __construct(private EmployeeRepositoryInterface $employeeRepository) {}

    /**
     * Register a new employee from public form
     */
    public function registerEmployee(RegisterEmployeeDTO $dto): Employee
    {
        return DB::transaction(function () use ($dto) {
            // Find or create department
            $department = $this->findOrCreateDepartment($dto->getDepartment());
            
            // Find or create position in the department
            $position = $this->findOrCreatePosition($dto->getPosition(), $department);
            
            // Find manager if provided
            $manager = null;
            if ($dto->getManagerEmail()) {
                $manager = Employee::where('email', $dto->getManagerEmail())->first();
                if (!$manager) {
                    throw new Exception('Manager with email ' . $dto->getManagerEmail() . ' not found');
                }
            }

            // Generate employee ID
            $employeeId = $this->generateEmployeeId();

            // Validate unique constraints
            $this->validateUniqueFields($dto->getEmail(), $dto->getNationalId());

            // Create employee record
            $employeeData = [
                'name' => $dto->getName(),
                'email' => $dto->getEmail(),
                'personal_email' => $dto->getPersonalEmail(),
                'phone' => $dto->getPhone(),
                'business_phone' => $dto->getBusinessPhone(),
                'gender' => $dto->getGender(),
                'marital_status' => $dto->getMaritalStatus(),
                'national_id' => $dto->getNationalId(),
                'date_of_birth' => $dto->getDateOfBirth(),
                'address' => $dto->getAddress(),
                'emergency_contact_name' => $dto->getEmergencyContactName(),
                'emergency_contact_relation' => $dto->getEmergencyContactRelation(),
                'emergency_contact_phone' => $dto->getEmergencyContactPhone(),
                'employee_id' => $employeeId,
                'department_id' => $department->id,
                'position_id' => $position->id,
                'level' => EmployeeLevel::from($dto->getEmployeeLevel()),
                'contract_type' => $dto->getContractType(),
                'reporting_to' => $manager?->id,
                'company_date_of_joining' => $dto->getCompanyJoiningDate() ?? now()->toDateString(),
                'password' => Hash::make($dto->getPassword()),
                'email_verified_at' => now(),
                'status' => false, // Inactive until HR approval
                'password_set_at' => now(),
            ];

            return $this->employeeRepository->create($employeeData);
        });
    }

    /**
     * Find or create department
     */
    private function findOrCreateDepartment(string $departmentName): Department
    {
        $department = Department::where('name', 'like', "%{$departmentName}%")->first();
        
        if (!$department) {
            $department = Department::create([
                'name' => $departmentName,
                'status' => true,
            ]);
        }

        return $department;
    }

    /**
     * Find or create position in department
     */
    private function findOrCreatePosition(string $positionName, Department $department): Position
    {
        $position = Position::where('name', 'like', "%{$positionName}%")
            ->where('department_id', $department->id)
            ->first();
        
        if (!$position) {
            $position = Position::create([
                'name' => $positionName,
                'department_id' => $department->id,
                'status' => true,
            ]);
        }

        return $position;
    }

    /**
     * Generate unique employee ID
     */
    private function generateEmployeeId(): string
    {
        $prefix = 'EMP';
        $lastEmployee = Employee::where('employee_id', 'like', $prefix . '%')
            ->orderBy('employee_id', 'desc')
            ->first();

        if ($lastEmployee) {
            $lastNumber = (int) substr($lastEmployee->employee_id, strlen($prefix));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Validate unique fields
     */
    private function validateUniqueFields(string $email, string $nationalId): void
    {
        if (Employee::where('email', $email)->exists()) {
            throw new Exception('Email address is already registered');
        }

        if (Employee::where('national_id', $nationalId)->exists()) {
            throw new Exception('National ID is already registered');
        }
    }
}