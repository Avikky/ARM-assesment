<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use Validator;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class EmployeeRegistration extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'surname' => 'required|string',
            'firstname' => 'required|string',
            'employer_code' => 'required',
            'email' => 'required|email',
            'mobile_no' => 'required|min:11',
            'state_of_residence' => 'required|string',
            'address' => 'required|string',
            'next_of_kin_surname' => 'required|string',
            'next_of_kin_firstname' => 'required|string',
            'next_of_kin_email' => 'required|email',
            'next_of_kin_phone' => 'required|numeric',
        ]);

        if($validator->fails()){
            return response()->json(['success' => false, 'Validation Error.' => $validator->errors()]);
        }
        
        $employee = new Employee;

        $otp = mt_rand(1000, 9999);

        $employee->surname = $request->surname;
        $employee->firstname = $request->surname;
        $employee->employer_code = $request->employer_code;
        $employee->email = $request->email;
        $employee->mobile_no = $request->mobile_no;
        $employee->address = $request->address;
        $employee->state_of_residence = $request->state_of_residence;
        $employee->nk_surname = $request->next_of_kin_surname;
        $employee->nk_firstname = $request->next_of_kin_firstname;
        $employee->nk_email = $request->next_of_kin_email;
        $employee->nk_phone = $request->next_of_kin_phone;
        $employee->otp = $otp;
        

        $employee->expires_at = Carbon::now()->addSeconds(3600);

        $employee->save();

        
        $name = $request->surname.' '. $request->lastname;
        $email = $request->email;
        $otp_code = $employee->otp;

        $data = array('otp_code'=> $otp_code, 'name'=> $name);

        Mail::send('emails.verify', $data, function($message)use($name,$email){
            $message
                ->to($email, $name)
                ->subject('Verify Your Account');
        });

        return response()->json(['success' => true, 'message' => 'A verification code has been sent to your email']);
        
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|integer',
            'email' => 'required|string'
        ]);

        if($validator->fails()){
            return response()->json(['success' => false,'Validation Error.' => $validator->errors()]);
        }

        $employee = Employee::where('email', $request->email)->first();

        if($employee == null){
            return response()->json(['success' => false, 'message' => 'User does not exist']);
        }

        //expiring otp

        // if($employee->otp < Carbon::now()){

        // }

        if($employee->otp !== $request->otp){
            return response()->json(['success' => false, 'message' => 'invalid otp']);
        }

        $accountNo = 'PEN'.mt_rand(10000, 999999999999);

        $employee->acct_no = $accountNo;
        $employee->is_verified = 1;
        $employee->reg_status = 1; //Employee reg_status 1 is active while default is 0 = pending
        $employee->otp = null;

        $$employee->save();


        return response()->json(['success' => true, 'message' => $accountNo]);

    }
}
