<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    // GET /api/students
    public function index()
    {
        $students = Student::all();
        return response()->json(['status' => 'success', 'data' => $students], 200);
    }

    public function store(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'name' => 'required\string\max:255',
            'email' => 'required\email\unqiue:students,email',
            'age' => 'required\integer\min:0',
            'course' => 'nullable\string\max:255',
        ]);

        $student = Student::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Student created successfully',
            'data' => $student,
        ], 200);
    }

    public function show(Student $student)
    {
        // GET /api/students/{id}
        return response()->json([
                'status' => 'success',
                'message' => 'Student updated successfully',
                'data' => $student,
            ], 200);
    }

    public function update(Request $request, Student $student)
    {
        //
        $validated = $request ->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:students,email,' . $student->id,
            'age' => 'nullable|integer|min:0',
            'course' => 'nullable|string|max:255',
        ]);

        $student->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Student updated successfully',
            'data' => $student,
        ], 200);
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Student deleted successfully',
        ]);
    }
}