<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Employer;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class EmployerRegistration extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'surname' => 'required|string',
            'firstname' => 'required|string',
            'employer_name' => 'required|string',
            'email' => 'required|email|unique:employers',
            'mobile_no' => 'required|min:11',
            'state_of_location' => 'required|string',
            'address' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json(['success' => false, 'Validation Error.' => $validator->errors()]);
        }
        
        $employer = new Employer;

        $otp = mt_rand(1000, 9999);

        $employer->surname = $request->surname;
        $employer->firstname = $request->surname;
        $employer->employer_name = $request->employer_name;
        $employer->email = $request->email;
        $employer->mobile_no = $request->mobile_no;
        $employer->address = $request->address;
        $employer->state_of_location = $request->state_of_location;
     
        $employer->otp = $otp;
        

        $employer->expires_at = Carbon::now()->addSeconds(3600);

        $employer->save();

        
        $name = $request->surname.' '. $request->lastname;
        $email = $request->email;
        $otp_code = $employer->otp;

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

        $employer = Employer::where('email', $request->email)->first();

        if($employer == null){
            return response()->json(['success' => false, 'message' => 'User does not exist']);
        }

        if($employer->is_verified == 1){
            return response()->json(['success' => false, 'message' => 'oops your account is already verified proceed to login']);
        }


        //expiring otp

        if(Carbon::now() > $employer->expires_at){
            return response()->json(['success' => false, 'message' => 'otp token has expired']);
        }



        if($employer->otp !== $request->otp){
            return response()->json(['success' => false, 'message' => 'invalid otp']);
        }


        $accountNo = 'EMP'.mt_rand(10000, 999999999999);

        $employer->acct_no = $accountNo;
        $employer->is_verified = 1;
        $employer->reg_status = 1; //Employee reg_status 1 is active while default is 0 = pending

        $employer->save();


        return response()->json(['success' => true, 'message' => $accountNo]);

    }
}
