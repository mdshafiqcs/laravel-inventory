<?php 

namespace App\Service;

use App\Enum\UserRole;
use App\Exceptions\GeneralException;
use App\Exceptions\NotFoundException;
use App\Models\Inventory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService {

    static public function createUser(Request $request) {

        try {
            
            $user = new User();

            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->role = UserRole::USER->value;
            $user->remember_token = Str::random(60) . '-' . time();
            $user->created_at = Carbon::now();

            $user->save();

            $user->token = $user->createToken($request->email)->plainTextToken;

            return $user;

        } catch (\Exception $e) {
            throw $e;
        }
    }

    static public function sendVerificationMail(User $user) {

        /* we will send a verification link to user's email. currently we are not using verification. it will be used later on. at this moment, sending emails without queue will have bad impacts on app performance. 
            $link = url('/verify-email') . '/' . $user->remember_token ;
            Mail::to($user)->send(new EmailVerificationMail($link));
         */

    }
}