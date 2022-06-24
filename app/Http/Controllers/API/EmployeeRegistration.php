<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\User;
use Validator;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class EmployeeRegistration extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'surname' => 'required|string',
            'firstname' => 'required|string',
            'employer_code' => 'required',
            'email' => 'required|email|unique:users',
            'mobile_no' => 'required|min:11',
            'state_of_residence' => 'required|string',
            'address' => 'required|string',
            'next_of_kin_surname' => 'required|string',
            'next_of_kin_firstname' => 'required|string',
            'next_of_kin_email' => 'required|email',
            'next_of_kin_phone' => 'required|numeric',
            'password' => 'required|string',
            'confirm_password' => 'required|same:password',
        ]);

        if($validator->fails()){
            return response()->json(['success' => false, 'Validation Error.' => $validator->errors()]);
        }

        $otp = mt_rand(1000, 9999);

        $password = bcrypt($request->password);
        
        $user = new User;

        $user->surname = $request->surname;
        $user->firstname = $request->firstname;
        $user->password = $password;
        $user->user_type = 'employee';
        $user->email = $request->email;
        $user->mobile_no = $request->mobile_no;
        $user->otp = $otp;
        

        $user->expired_at = Carbon::now()->addSeconds(3600);

        $user->save();
        
        $employee = new Employee; 

        $employee->user_id = $user->id;
        $employee->employer_code = $request->employer_code;
        $employee->address = $request->address;
        $employee->state_of_residence = $request->state_of_residence;
        $employee->nk_surname = $request->next_of_kin_surname;
        $employee->nk_firstname = $request->next_of_kin_firstname;
        $employee->nk_email = $request->next_of_kin_email;
        $employee->nk_phone = $request->next_of_kin_phone;

        $employee->save();

        
        $name = $request->surname.' '. $request->lastname;
        $email = $request->email;
        $otp_code = $user->otp;

        $data = array('otp_code'=> $otp_code, 'name'=> $name);

        Mail::send('emails.verify', $data, function($message)use($name,$email){
            $message
                ->to($email, $name)
                ->subject('Verify Your Account');
        });

        $response = [
            'success' => true,
            'message' => 'A verification code has been sent to your email',
            'user' => User::with('employee_details')->where('id', $user->id)->first()
        ];

        return response()->json($response);
        
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

        $user = User::where('email', $request->email)->first();

        if($user == null){
            return response()->json(['success' => false, 'message' => 'User does not exist']);
        }

        if($user->is_verified == 1){
            return response()->json(['success' => false, 'message' => 'oops your account is already verified proceed to login']);
        }

        //expiring otp

        if(Carbon::now() > $user->expired_at){
            return response()->json(['success' => false, 'message' => 'otp token has expired']);
        }

        if($user->otp !== $request->otp){
            return response()->json(['success' => false, 'message' => 'invalid otp']);
        }

        $accountNo = 'PEN'.mt_rand(10000, 999999999999);

        $user->acct_no = $accountNo;
        $user->is_verified = 1;
        $user->is_verified = 1;
        $user->email_verified_at = Carbon::now();
        $user->reg_status = 1; //user reg_status 1 is active while default is 0 = pending
        $user->employee_details->update(['acct_no' => $accountNo]);

        $user->save();

        $response = [
            'success' => true,
            'data' => [
                'account_no' => $accountNo,
                'message' => 'Your account has been successfully verified proceed to login',
                'user' => User::with('employee_details')->where('id', $user->id)->first()
            ]
        ];

        
        return response()->json($response);

    }


    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if($validator->fails()){
            return response()->json(['success' => false,'Validation Error.' => $validator->errors()]);
        }

        $user = User::where('email', $request->email)->first();
        $otp = mt_rand(1000, 9999);

        if($user == null){
            return response()->json(['success' => false, 'message' => 'User does not exist']);
        }

        $user->expired_at = Carbon::now()->addSeconds(3600);
        $user->otp = $otp;

        $user->save();

        $name = $user->surname.' '. $user->lastname;
        $email = $request->email;
        $otp_code = $user->otp;

        $data = array('otp_code'=> $otp_code, 'name'=> $name);

        Mail::send('emails.verify', $data, function($message)use($name,$email){
            $message
                ->to($email, $name)
                ->subject('Verify Your Account');
        });

        $response = [
            'success' => true,
            'message' => 'A verification code has been sent to your email'
        ];

        return response()->json($response);

    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'acct_no' => 'required|string',
            'password' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json(['success' => false,'Validation Error.' => $validator->errors()]);
        }


        if(Auth::attempt(['acct_no' => $request->acct_no, 'password' => $request->password])){ 
            $user = Auth::user(); 
            Auth::login($user);

            $response = [
                'success' => true,
                'access_token' => $user->createToken('MyApp')->accessToken,
                'message' => 'User logged in successfully.',
                'user' => User::with('employee_details')->where('id', $user->id)->first()
            ];
            
         
            return response()->json($response);

        }else{ 
            $response = [
                'success' => false,
                'message' => 'Error: Unauthenticated',
            ];
            
            return response()->json($response);
        } 
    } 

    public function logout()
    {
        $user = Auth::user()->token();
        $user->revoke();

        $response = [
            'success' => true,
            'message' => 'User logged out successfully.',
        ];

        return response()->json($response);
    }
}
