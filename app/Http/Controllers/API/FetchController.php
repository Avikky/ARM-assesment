<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;


class FetchController extends Controller
{
    public function fetchEmployees()
    {
        $employees = User::with('employee_details')->paginate(10);

        return response()->json($employees);
    }

    public function fetchEmployers()
    {
        $employers = User::with('employer_details')->paginate(10);

        return response()->json($employers);
    }
}
