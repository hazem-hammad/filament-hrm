<?php

namespace App\Imports;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Enum\MaritalStatus;
use App\Enum\ContractType;
use App\Enum\SocialInsuranceStatus;
use App\Notifications\EmployeeWelcomeNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Throwable;

class EmployeeImport implements
    ToCollection,
    WithHeadingRow,
    WithValidation
{
    use Importable;

    protected array $importedEmployees = [];
    protected array $importErrors = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            try {
                $this->processEmployeeRow($row->toArray(), $index + 2); // +2 because of header row and 0-based index
            } catch (Throwable $e) {
                Log::error('Employee import error on row ' . ($index + 2), [
                    'error' => $e->getMessage(),
                    'row_data' => $row->toArray(),
                    'trace' => $e->getTraceAsString()
                ]);
                $this->importErrors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
            }
        }

        // Send welcome emails to all imported employees
        $this->sendWelcomeEmails();
    }

    protected function processEmployeeRow(array $row, int $rowNumber): void
    {
        // Clean and validate data
        $cleanedRow = $this->cleanRowData($row);

        // Validate required fields
        $this->validateRequiredFields($cleanedRow, $rowNumber);

        // Find or fail to get department and position
        $department = $this->findDepartment($cleanedRow['department'], $rowNumber);
        $position = $this->findPosition($cleanedRow['position'], $rowNumber);

        // Find manager if provided
        $manager = null;
        if (!empty($cleanedRow['manager_email'])) {
            $manager = $this->findManager($cleanedRow['manager_email'], $rowNumber);
        }

        // Generate employee ID if not provided
        if (empty($cleanedRow['employee_id'])) {
            $cleanedRow['employee_id'] = $this->generateEmployeeId($department);
        }

        // Check if employee already exists
        if (Employee::where('email', $cleanedRow['email'])->exists()) {
            throw new \Exception("Employee with email {$cleanedRow['email']} already exists");
        }

        if (Employee::where('employee_id', $cleanedRow['employee_id'])->exists()) {
            throw new \Exception("Employee with ID {$cleanedRow['employee_id']} already exists");
        }

        // Generate password
        $password = $this->generateSecurePassword();
        $hashedPassword = Hash::make($password);

        // Create employee
        $employee = Employee::create([
            'name' => $cleanedRow['name'],
            'email' => $cleanedRow['email'],
            'personal_email' => $cleanedRow['personal_email'] ?: null,
            'phone' => $cleanedRow['phone'],
            'business_phone' => $cleanedRow['business_phone'] ?: null,
            'gender' => $cleanedRow['gender'],
            'marital_status' => $cleanedRow['marital_status'],
            'national_id' => $cleanedRow['national_id'],
            'date_of_birth' => $cleanedRow['date_of_birth'],
            'address' => $cleanedRow['address'],
            'emergency_contact_name' => $cleanedRow['emergency_contact_name'] ?: null,
            'emergency_contact_relation' => $cleanedRow['emergency_contact_relation'] ?: null,
            'emergency_contact_phone' => $cleanedRow['emergency_contact_phone'] ?: null,
            'employee_id' => $cleanedRow['employee_id'],
            'department_id' => $department->id,
            'position_id' => $position->id,
            'level' => $cleanedRow['employee_level'] ?? 'entry',
            'contract_type' => $cleanedRow['contract_type'],
            'social_insurance_status' => $cleanedRow['social_insurance_status'],
            'social_insurance_number' => $cleanedRow['social_insurance_number'],
            'reporting_to' => $manager?->id,
            'company_date_of_joining' => $cleanedRow['company_joining_date'],
            'password' => $hashedPassword,
            'email_verified_at' => now(),
            'status' => true,
        ]);

        // Store for welcome email sending
        $this->importedEmployees[] = [
            'employee' => $employee,
            'password' => $password,
            'row_number' => $rowNumber
        ];

        Log::info('Employee imported successfully', [
            'employee_id' => $employee->employee_id,
            'email' => $employee->email,
            'row_number' => $rowNumber
        ]);
    }

    protected function cleanRowData(array $row): array
    {
        return [
            'name' => trim($row['name'] ?? ''),
            'email' => strtolower(trim($row['email'] ?? '')),
            'personal_email' => strtolower(trim($row['personal_email'] ?? '')),
            'phone' => trim(strval($row['phone'] ?? '')), // Convert to string to handle numeric phones
            'business_phone' => trim(strval($row['business_phone'] ?? '')),
            'gender' => strtolower(trim($row['gender'] ?? '')),
            'marital_status' => strtolower(trim($row['marital_status'] ?? 'single')),
            'national_id' => trim($row['national_id'] ?? ''),
            'date_of_birth' => $this->parseDate($row['date_of_birth'] ?? ''),
            'address' => trim($row['address'] ?? ''),
            'emergency_contact_name' => trim($row['emergency_contact_name'] ?? ''),
            'emergency_contact_relation' => trim($row['emergency_contact_relation'] ?? ''),
            'emergency_contact_phone' => trim(strval($row['emergency_contact_phone'] ?? '')),
            'employee_id' => trim($row['employee_id'] ?? ''),
            'department' => trim($row['department'] ?? ''),
            'position' => trim($row['position'] ?? ''),
            'employee_level' => strtolower(trim($row['employee_level'] ?? 'entry')),
            'contract_type' => strtolower(trim($row['contract_type'] ?? 'permanent')),
            'social_insurance_status' => strtolower(trim($row['social_insurance_status'] ?? 'not_applicable')),
            'social_insurance_number' => trim($row['social_insurance_number'] ?? ''),
            'manager_email' => strtolower(trim($row['manager_email'] ?? '')),
            'company_joining_date' => $this->parseDate($row['company_joining_date'] ?? ''),
        ];
    }

    protected function parseDate(?string $dateString): ?string
    {
        if (empty($dateString)) {
            return null;
        }

        try {
            // Try different date formats
            $formats = [
                'Y-m-d',
                'd/m/Y',
                'm/d/Y',
                'd-m-Y',
                'm-d-Y',
                'Y/m/d',
            ];

            foreach ($formats as $format) {
                $date = \DateTime::createFromFormat($format, $dateString);
                if ($date !== false) {
                    return $date->format('Y-m-d');
                }
            }

            // Try to parse with Carbon
            return \Carbon\Carbon::parse($dateString)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function validateRequiredFields(array $row, int $rowNumber): void
    {
        $requiredFields = ['name', 'email', 'phone', 'gender', 'marital_status', 'national_id', 'department', 'position', 'contract_type', 'social_insurance_status'];

        foreach ($requiredFields as $field) {
            if (empty($row[$field])) {
                throw new \Exception("Required field '{$field}' is missing or empty");
            }
        }

        // Validate email format
        if (!filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("Invalid email format: {$row['email']}");
        }

        // Validate gender
        if (!in_array($row['gender'], ['male', 'female'])) {
            throw new \Exception("Gender must be 'male' or 'female', got: {$row['gender']}");
        }

        // Validate marital status
        $validMaritalStatuses = array_keys(MaritalStatus::options());
        if (!in_array($row['marital_status'], $validMaritalStatuses)) {
            throw new \Exception("Invalid marital status: {$row['marital_status']}. Must be one of: " . implode(', ', $validMaritalStatuses));
        }

        // Validate contract type
        $validContractTypes = array_keys(ContractType::options());
        if (!in_array($row['contract_type'], $validContractTypes)) {
            throw new \Exception("Invalid contract type: {$row['contract_type']}. Must be one of: " . implode(', ', $validContractTypes));
        }

        // Validate social insurance status
        $validSocialInsuranceStatuses = array_keys(SocialInsuranceStatus::options());
        if (!in_array($row['social_insurance_status'], $validSocialInsuranceStatuses)) {
            throw new \Exception("Invalid social insurance status: {$row['social_insurance_status']}. Must be one of: " . implode(', ', $validSocialInsuranceStatuses));
        }

        // Validate employee level
        $validLevels = ['internship', 'entry', 'junior', 'mid', 'senior', 'lead', 'manager'];
        if (!in_array($row['employee_level'], $validLevels)) {
            throw new \Exception("Invalid employee level: {$row['employee_level']}. Must be one of: " . implode(', ', $validLevels));
        }
    }

    protected function findDepartment(string $departmentName, int $rowNumber): Department
    {
        $department = Department::where('name', 'like', "%{$departmentName}%")->first();

        if (!$department) {
            throw new \Exception("Department '{$departmentName}' not found");
        }

        return $department;
    }

    protected function findPosition(string $positionName, int $rowNumber): Position
    {
        $position = Position::where('name', 'like', "%{$positionName}%")->first();

        if (!$position) {
            throw new \Exception("Position '{$positionName}' not found");
        }

        return $position;
    }

    protected function findManager(string $managerEmail, int $rowNumber): ?Employee
    {
        $manager = Employee::where('email', $managerEmail)->first();

        if (!$manager) {
            Log::warning('Manager not found during import', [
                'manager_email' => $managerEmail,
                'row_number' => $rowNumber
            ]);
            // Don't throw error, just log warning and continue without manager
        }

        return $manager;
    }

    protected function generateEmployeeId(Department $department): string
    {
        $prefix = strtoupper(substr($department->name, 0, 3));
        $lastEmployee = Employee::where('employee_id', 'like', $prefix . '%')
            ->orderBy('employee_id', 'desc')
            ->first();

        if ($lastEmployee) {
            $lastNumber = (int) substr($lastEmployee->employee_id, 3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    protected function generateSecurePassword(): string
    {
        return Str::password(12, true, true, true, false);
    }

    protected function sendWelcomeEmails(): void
    {
        foreach ($this->importedEmployees as $importData) {
            try {
                $employee = $importData['employee'];
                $password = $importData['password'];

                // Send welcome email with password. Use sendNow to avoid requiring a running queue worker
                // For the existing notification, we need a password setup token as well
                $passwordSetupToken = \Illuminate\Support\Str::random(64);
                Notification::sendNow($employee, new EmployeeWelcomeNotification($password, $passwordSetupToken));

                Log::info('Welcome email sent', [
                    'employee_id' => $employee->employee_id,
                    'email' => $employee->email
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send welcome email', [
                    'employee_id' => $importData['employee']->employee_id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:employees,email'],
            'personal_email' => ['nullable', 'email'],
            'phone' => ['required', 'max:20'], // Remove string validation since Excel converts it to number
            'business_phone' => ['nullable', 'max:20'],
            'gender' => ['required', 'in:male,female'],
            'marital_status' => ['required', 'in:' . implode(',', array_keys(MaritalStatus::options()))],
            'national_id' => ['required', 'max:255'],
            'emergency_contact_name' => ['nullable', 'max:255'],
            'emergency_contact_relation' => ['nullable', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'max:20'],
            'contract_type' => ['required', 'in:' . implode(',', array_keys(ContractType::options()))],
            'social_insurance_status' => ['required', 'in:' . implode(',', array_keys(SocialInsuranceStatus::options()))],
            'social_insurance_number' => ['nullable', 'max:255'],
            'department' => ['required', 'max:255'],
            'position' => ['required', 'max:255'],
        ];
    }

    public function getImportedCount(): int
    {
        return count($this->importedEmployees);
    }

    public function getErrors(): array
    {
        return $this->importErrors;
    }
}
