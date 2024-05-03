<?php

namespace App\Http\Controllers\Api;

use App\Enum\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

use App\Enum\Status;
use App\Traits\ResponseTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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

            DB::beginTransaction();

            $user = new User();

            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->role = UserRole::USER->value;
            $user->remember_token = Str::random(60) . '-' . time();
            $user->created_at = Carbon::now();

            $user->save();

            $token = $user->createToken($request->email)->plainTextToken;
            $user->token = $token;

            /* we will send a verification link to user's email. currently we are not using verification. it will be used later on. at this moment, sending emails without queue will have bad impacts on app performance. 
                $link = url('/verify-email') . '/' . $user->remember_token ;
                Mail::to($user)->send(new EmailVerificationMail($link));
            */

            DB::commit();

            return $this->successResponse($user, "Registration Successfull");
            
        } catch(ValidationException $e){
            DB::rollBack();
            return $this->errorResponse($e->getMessage(), 422, Status::ERROR);

        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->errorResponse("Internal Server Error", 500, Status::ERROR);
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

        } catch (\Throwable $th) {
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

        } catch (\Throwable $th) {
            return $this->errorResponse("Internal Server Error", 500, Status::ERROR);
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
         catch (\Throwable $th) {
            return $this->errorResponse("Internal Server Error", 500, Status::ERROR);
        }
    }

    public function user(){
        try {

            $user = User::findOrFail(auth()->user()->id);

            return $this->successResponse($user);

        } catch(ModelNotFoundException $e){
            return $this->errorResponse("User not found", 404, Status::ERROR);
        }
         catch (\Throwable $th) {
            return $this->errorResponse("Internal Server Error", 500, Status::ERROR);
        }
    }

}
