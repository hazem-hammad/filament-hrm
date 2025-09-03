<?php

namespace App\DTOs\V1\Employee;

use App\DTOs\Common\AbstractDTO;

final class RegisterEmployeeDTO extends AbstractDTO
{
    protected string $name;
    protected string $email;
    protected ?string $personalEmail = null;
    protected string $phone;
    protected ?string $businessPhone = null;
    protected string $gender;
    protected string $maritalStatus;
    protected string $nationalId;
    protected ?string $dateOfBirth = null;
    protected ?string $address = null;
    protected ?string $emergencyContactName = null;
    protected ?string $emergencyContactRelation = null;
    protected ?string $emergencyContactPhone = null;
    protected string $department;
    protected string $position;
    protected string $employeeLevel;
    protected string $contractType;
    protected ?string $managerEmail = null;
    protected ?string $companyJoiningDate = null;
    protected string $password;
    protected string $passwordConfirmation;

    protected function map(array $data): bool
    {
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->personalEmail = $data['personal_email'] ?? null;
        $this->phone = $data['phone'];
        $this->businessPhone = $data['business_phone'] ?? null;
        $this->gender = $data['gender'];
        $this->maritalStatus = $data['marital_status'];
        $this->nationalId = $data['national_id'];
        $this->dateOfBirth = $data['date_of_birth'] ?? null;
        $this->address = $data['address'] ?? null;
        $this->emergencyContactName = $data['emergency_contact_name'] ?? null;
        $this->emergencyContactRelation = $data['emergency_contact_relation'] ?? null;
        $this->emergencyContactPhone = $data['emergency_contact_phone'] ?? null;
        $this->department = $data['department'];
        $this->position = $data['position'];
        $this->employeeLevel = $data['employee_level'];
        $this->contractType = $data['contract_type'];
        $this->managerEmail = $data['manager_email'] ?? null;
        $this->companyJoiningDate = $data['company_joining_date'] ?? null;
        $this->password = $data['password'];
        $this->passwordConfirmation = $data['password_confirmation'];
        
        return true;
    }

    public function getName(): string { return $this->name; }
    public function getEmail(): string { return $this->email; }
    public function getPersonalEmail(): ?string { return $this->personalEmail; }
    public function getPhone(): string { return $this->phone; }
    public function getBusinessPhone(): ?string { return $this->businessPhone; }
    public function getGender(): string { return $this->gender; }
    public function getMaritalStatus(): string { return $this->maritalStatus; }
    public function getNationalId(): string { return $this->nationalId; }
    public function getDateOfBirth(): ?string { return $this->dateOfBirth; }
    public function getAddress(): ?string { return $this->address; }
    public function getEmergencyContactName(): ?string { return $this->emergencyContactName; }
    public function getEmergencyContactRelation(): ?string { return $this->emergencyContactRelation; }
    public function getEmergencyContactPhone(): ?string { return $this->emergencyContactPhone; }
    public function getDepartment(): string { return $this->department; }
    public function getPosition(): string { return $this->position; }
    public function getEmployeeLevel(): string { return $this->employeeLevel; }
    public function getContractType(): string { return $this->contractType; }
    public function getManagerEmail(): ?string { return $this->managerEmail; }
    public function getCompanyJoiningDate(): ?string { return $this->companyJoiningDate; }
    public function getPassword(): string { return $this->password; }
    public function getPasswordConfirmation(): string { return $this->passwordConfirmation; }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'personal_email' => $this->personalEmail,
            'phone' => $this->phone,
            'business_phone' => $this->businessPhone,
            'gender' => $this->gender,
            'marital_status' => $this->maritalStatus,
            'national_id' => $this->nationalId,
            'date_of_birth' => $this->dateOfBirth,
            'address' => $this->address,
            'emergency_contact_name' => $this->emergencyContactName,
            'emergency_contact_relation' => $this->emergencyContactRelation,
            'emergency_contact_phone' => $this->emergencyContactPhone,
            'department' => $this->department,
            'position' => $this->position,
            'employee_level' => $this->employeeLevel,
            'contract_type' => $this->contractType,
            'manager_email' => $this->managerEmail,
            'company_joining_date' => $this->companyJoiningDate,
            'password' => $this->password,
        ];
    }
}