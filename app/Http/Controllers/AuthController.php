<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponse;
use App\Models\User;
use App\Notifications\VerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;
use Str;

class AuthController extends Controller
{
    use ApiResponse;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'setPassword']]);
    }
    public function register(Request $request)
    {
        $req = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'full_name' => 'required|string|min:5',
        ]);

        if ($req->fails()) {
            return $this->apiResponse(422, 'ValidatorErrors', $req->errors() );
        }
        $user = User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
        ]);
        if($user)
        {
            $user->activation_token = Str::random(40);
            $user->save();

            $user->notify(new VerifyEmail($user));
        }
        return $this->apiResponse(201,'Registered');

    }
    public function setPassword(Request $request)
    {
        $req = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
            'password' => 'required|min:6'
        ]);

        if ($req->fails()) {
            return $this->apiResponse(422, 'ValidatorErrors', $req->errors() );
        }
        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password)
        ]);
        return $this->login();
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
        return $this->respondWithToken($token);
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
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        $data = [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ];
        return $this->apiResponse(200,'success', null, $data);
    }
}
