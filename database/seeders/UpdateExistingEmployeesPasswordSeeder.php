<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;

class UpdateExistingEmployeesPasswordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder updates existing employees who don't have passwords
     * by generating default passwords based on their name and birthday.
     */
    public function run(): void
    {
        // Get all employees without passwords
        $employees = Employee::whereNull('password')->orWhere('password', '')->get();

        if ($employees->isEmpty()) {
            $this->command->info('No employees found without passwords.');
            return;
        }

        $this->command->info("Found {$employees->count()} employees without passwords. Updating...");

        foreach ($employees as $employee) {
            // Generate default password: FirstName@YYYYMMDD
            $firstName = explode(' ', trim($employee->name))[0];
            $birthdayFormatted = $employee->birthday ? $employee->birthday->format('Ymd') : date('Ymd');
            $defaultPassword = $firstName . '@' . $birthdayFormatted;

            // Update employee with password
            $employee->update([
                'password' => Hash::make($defaultPassword),
                'is_active' => true,
            ]);

            $this->command->info("Updated employee: {$employee->name} - Password: {$defaultPassword}");
        }

        $this->command->info('All existing employees have been updated with default passwords.');
        $this->command->warn('IMPORTANT: Please inform employees of their default passwords!');
    }
}

