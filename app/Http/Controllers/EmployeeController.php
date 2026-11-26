<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $department = $request->input('department');
        $status = $request->input('status');
        $sort = $request->input('sort', 'name');
        $direction = strtolower((string) $request->input('direction', 'asc')) === 'desc' ? 'desc' : 'asc';

        $allowedSorts = ['name', 'position', 'department', 'status', 'created_at'];
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'name';
        }

        // Single clean paginated query (removed 6 unused duplicate DB queries)
        $employees = Employee::query()
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('position', 'like', "%{$search}%")
                      ->orWhere('department', 'like', "%{$search}%");
                });
            })
            ->when($department && $department !== 'All Departments', function ($query) use ($department) {
                return $query->where('department', $department);
            })
            ->when($status && $status !== 'All Status', function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString();

        return view('employee', compact('employees'));
    }


    public function create()
    {
        return view('employees.create'); // Create a view for the form
    }

    

    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'status' => 'required|string|max:255',
        ]);

        // Save the data to the database (excluding _token)
        Employee::create($request->only(['name', 'position', 'department', 'status']));

        // Redirect to the employee list page
        return redirect()->route('employees.index')->with('success', 'Employee added successfully!');
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'status' => 'required|string|in:Active,Inactive,On Leave',
        ]);

        // Update employee with validated data
        $employee->update($validated);

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully');
    }


    public function destroy(Employee $employee)
    {
        // Delete the employee record
        $employee->delete();

        // Redirect back with a success message
        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully');
    }

    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }


   
}
