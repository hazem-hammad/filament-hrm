<?php

namespace App\Repositories\Interfaces;

use App\Models\Employee;

interface EmployeeRepositoryInterface
{
    /**
     * Create a new employee
     */
    public function create(array $data): Employee;

    /**
     * Find employee by ID
     */
    public function findById(int $id): ?Employee;

    /**
     * Find employee by email
     */
    public function findByEmail(string $email): ?Employee;

    /**
     * Update employee
     */
    public function update(Employee $employee, array $data): Employee;
}