<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
class EmployeeController extends Controller
{
    public function index(){
        $employees = Employee::all();
        return response()->json([
            'message' => 'Employees fetched successfully',
            'employees' => $employees,
        ], 200);
    }
    public function show($id){
        $employee = Employee::find($id);
        return response()->json([
            'message' => 'Employee fetched successfully',
            'employee' => $employee,
        ], 200);
    }

    public function update(Request $request, $id){
        $employee = Employee::find($id);
        $employee->update($request->all());
        return response()->json([
            'message' => 'Employee updated successfully',
            'employee' => $employee,
        ], 200);
    }
    public function destroy($id){
        $employee = Employee::find($id);
        $employee->delete();
        return response()->json([
            'message' => 'Employee deleted successfully',
        ], 200);
    }
}
