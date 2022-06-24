<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeResource;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Employer;
use App\Http\Resources\EmployerResource;


class FetchController extends Controller
{
    public function fetchEmployees()
    {
        $employees = Employee::paginate(10);

        return EmployeeResource::collection($employees);
    }

    public function fetchEmployers()
    {
        $employers = Employer::paginate(10);

        return EmployerResource::collection($employers);
    }
}
