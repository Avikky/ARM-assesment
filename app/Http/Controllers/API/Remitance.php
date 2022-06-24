<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Validator;

class Remitance extends Controller
{
    public function remitance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employer_id' => 'required|integer',
            'employee_account_no' => 'required|string',
            'amount' => 'required|integer',
        ]);

        if($validator->fails()){
            return response()->json(['success' => false, 'Validation Error.' => $validator->errors()]);
        }

        $employee = Employee::where('acct_no', $request->employee_account_no)->first();

        if($employee == null){
            return response()->json(['success' => false, 'message' => 'employee does not exist']);
        }

        $transaction = new Transaction;

        $transaction->employee_id = $employee->id;
        $transaction->employer_id = $request->employer_id;
        $transaction->acct_id = $request->employee_account_no;
        $transaction->amount = $request->amount;
        $transaction->trans_type = 1; // 1 is credit transaction while 0 is debit transaction

        $transaction->save();

        return response()->json(['success' => true, 'message' => 'Employee account has been credit successfully']);


    }
}
