<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

use App\Enum\Status;
use App\Service\AuthService;
use App\Traits\ResponseTrait;

class AuthController extends Controller
{

    use ResponseTrait;

    public function register(Request $request){

        try {

            $request->validate([
                'name' => 'required|min:3',
                'email' => 'required',
                'password' => 'required|min:6',
            ]);

            $user = User::findByEmail($request->email);

            if($user){
               return $this->errorResponse('User already exists with this email');
            }

            $user = AuthService::createUser($request);

            AuthService::sendVerificationMail($user);

            return $this->successResponse($user, "Registration Successfull");
            
        } 
        // catch(ValidationException $e){
        //     return $this->errorResponse($e->getMessage(), 422, Status::ERROR);
        // } 
        catch (\Exception $e) {
            return $this->errorResponse("Something went wrong", 500, Status::ERROR);
        }
    }

    
    public function verifyEmail($token){
        try {

            $user = User::where('remember_token', $token)->first();

            if(!$user){
                $message = "Invalid Token or account is already verified.";
                $color = "red";
                return view('email_verification', compact('message', 'color'));
            } 

            $user->is_email_verified = true;
            $user->email_verified_at = Carbon::now();
            $user->remember_token = null;
            $user->save();

            $message = "Email verification is successfull. Now you can use the app.";
            $color = "green";
            return view('email_verification', compact('message', 'color'));

        } catch (\Exception $e) {
            $message = "Opps! Something went wrong";
            $color = "black";
            return view('email_verification', compact('message', 'color'));
        }
    }

    public function login(Request $request){
        try {

            $request->validate([
                'email' => 'required',
                'password' => 'required|min:6',
            ]);
            
            $user = User::findByEmail($request->email);

            if(!$user){
               return $this->errorResponse('No User found with this email', 404);
            }
            if(!Hash::check($request->password, $user->password)){
                return $this->errorResponse('Wrong Password');
            }

            $user->token = $user->createToken($user->email)->plainTextToken;

            return $this->successResponse($user, "Login Successfull.");

        } catch(ValidationException $e){
            return $this->errorResponse($e->getMessage(), 422, Status::ERROR);

        } catch (\Exception $e) {
            return $this->errorResponse("Something went wrong", 500, Status::ERROR);
        }
    }

    public function logout(Request $request){
        try {

            $user = User::find(auth()->user()->id);

            $token = $request->bearerToken();
            $id = explode('|', $token)[0];

            if($request->hasSession()){
                $request->session()->flush();
            }

            $user->tokens()->where('id', $id)->delete();

            return $this->successResponse("","Logout Successfull.");

        } 
         catch (\Exception $e) {
            return $this->errorResponse("Something went wrong", 500, Status::ERROR);
        }
    }

    public function user(){
        try {

            $user = User::find(auth()->user()->id);

            if(!$user){
               return $this->errorResponse('No User found', 404);
            }

            return $this->successResponse($user);

        } catch (\Exception $e) {
            return $this->errorResponse("Something went wrong", 500, Status::ERROR);
        }
    }

}
