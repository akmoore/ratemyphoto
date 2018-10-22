<?php

namespace App\Http\Controllers;

use Auth;
use Mail;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Mail\EmailLoginVerify;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'setTokenForEmailLogin', 'validateToken']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if(auth()->user()->role === 'staff'){
            $user = auth()->user();
            if(!$user->has_logged_in){
                $user->has_logged_in = Carbon::now();
                $user->save();
            }
        }

        return $this->respondWithToken($token);
    }

    public function setTokenForEmailLogin(){
        $credentials = request(['email']);
        $user = User::where('email', $credentials['email'])->firstOrFail();
        
        //Create token
        $token = 'RMP-'. strtoupper(str_random(5));
        $user->email_verify_token = $token;
        $user->email_verify_token_exp = \Carbon\Carbon::now()->addMinutes(2);
        $user->save();

        //Send email to user
        Mail::to($user->email)->send(new EmailLoginVerify($user, $token));

        //Return response
        return response()->json(['message' => 'Email sent.']);

    }

    public function validateToken(){
        $credentials = request(['email', 'token']);
        $user = User::where('email', $credentials['email'])->firstOrFail();
        $now = \Carbon\Carbon::now();
        $isExpired = $user->email_verify_token_exp < $now;
        
        if($user->email_verify_token === $credentials['token'] && !$isExpired){
            $this->deleteUserToken($user);
            $token = auth()->login($user);

            if(auth()->user()->role === 'staff'){
                $user = auth()->user();
                if(!$user->has_logged_in){
                    $user->has_logged_in = Carbon::now();
                    $user->save();
                }
            }

            return $this->respondWithToken($token);
        }else{
            $this->deleteUserToken($user);
            return response()->json(['error' => 'Unauthenticated.', ], 401);
        }
    }

    private function deleteUserToken($user){
        $user->email_verify_token = null;
        $user->email_verify_token_exp = null;
        $user->save();
    }

    public function profile(){
        $profile = request(['email', 'new_password', 'current_password']);
        // dd($profile);

        if($profile['email']){
            auth()->user()->email = $profile['email'];
            auth()->user()->save();
            return response()->json(['message' => 'Email has been successfully updated.'], 200);
        }

        if($profile['new_password'] && auth()->user()->password == null){
            auth()->user()->password = bcrypt($profile['new_password']);
            auth()->user()->save();
            return response()->json(['message' => 'Password has been successfully added.', 'added' => true], 200);
        }

        if($profile['new_password'] && $profile['current_password']){
            if(!\Hash::check($profile['current_password'], auth()->user()->password)) return response()->json(['error' => 'Can\'t update. Current password is incorrect.'], 400);
            auth()->user()->password = bcrypt($profile['new_password']);
            auth()->user()->save();
            return response()->json(['message' => 'Password has been successfully updated.'], 200);
        }

        return response()->json(['error' => 'Unable to update profile. Ensure appropriate fields are filled.'], 400);

    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh(null, true));
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
