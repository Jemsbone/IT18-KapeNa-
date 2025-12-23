<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Employee;

class EmployeeController extends Controller
{
    /**
     * Get all employees
     */
    public function getAllEmployees()
    {
        try {
            $employees = Employee::orderBy('created_at', 'desc')
                ->get()
                ->map(function ($employee) {
                    // Calculate age from birthday
                    $age = null;
                    if ($employee->birthday) {
                        $age = $employee->birthday->diffInYears(now());
                    }
                    
                    return [
                        'id' => $employee->id,
                        'name' => $employee->name,
                        'birthday' => $employee->birthday ? $employee->birthday->format('Y-m-d') : null,
                        'age' => $age,
                        'sex' => $employee->sex,
                        'email' => $employee->email,
                        'phone' => $employee->phone,
                        'address' => $employee->address,
                        'created_at' => $employee->created_at,
                        'updated_at' => $employee->updated_at,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $employees
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Get all employees error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch employees.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Create a new employee
     */
    public function createEmployee(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'birthday' => 'required|date|before:today',
                'sex' => 'required|in:Male,Female,Other',
                'email' => 'nullable|email|unique:employees,email',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Generate a default password based on name and birthday
            // Format: FirstName@YYYYMMDD (e.g., John@19900115)
            $defaultPassword = $this->generateDefaultPassword($request->name, $request->birthday);

            $employee = Employee::create([
                'name' => $request->name,
                'birthday' => $request->birthday,
                'sex' => $request->sex,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'password' => $defaultPassword, // Will be automatically hashed by the model
                'is_active' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Employee created successfully!',
                'data' => $employee,
                'default_password' => $defaultPassword, // Return password so admin can inform employee
                'password_info' => 'Default password format: FirstName@YYYYMMDD'
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Create employee error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create employee.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Generate a default password for new employees
     * Format: FirstName@YYYYMMDD
     */
    private function generateDefaultPassword($name, $birthday)
    {
        // Get first name (before first space)
        $firstName = explode(' ', trim($name))[0];
        
        // Format birthday as YYYYMMDD
        $birthdayFormatted = date('Ymd', strtotime($birthday));
        
        // Combine to create password
        return $firstName . '@' . $birthdayFormatted;
    }

    /**
     * Delete an employee
     */
    public function deleteEmployee($id)
    {
        try {
            $employee = Employee::findOrFail($id);
            $employee->delete();

            return response()->json([
                'success' => true,
                'message' => 'Employee deleted successfully!'
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Delete employee error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete employee.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
