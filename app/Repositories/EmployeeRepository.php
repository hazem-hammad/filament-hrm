<?php

namespace App\Repositories;

use App\Repositories\Interfaces\EmployeeRepositoryInterface;
use App\Models\Employee;

class EmployeeRepository implements EmployeeRepositoryInterface
{
    /**
     * Create a new employee
     */
    public function create(array $data): Employee
    {
        return Employee::create($data);
    }

    /**
     * Find employee by ID
     */
    public function findById(int $id): ?Employee
    {
        return Employee::find($id);
    }

    /**
     * Find employee by email
     */
    public function findByEmail(string $email): ?Employee
    {
        return Employee::where('email', $email)->first();
    }

    /**
     * Update employee
     */
    public function update(Employee $employee, array $data): Employee
    {
        $employee->update($data);
        return $employee->fresh();
    }
}