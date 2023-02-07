<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;

use App\Http\Controllers\Controller;

use App\Models\Role;
use App\Http\Resources\UserCollection;
use App\Models\User;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


use DB;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:sanctum', ['except' => ['login','loginUser','register']]);
    }




    public function register(Request $request): JsonResponse
    {
        $request->validate([
             'username' => 'required|max:50|min:3|unique:users',
            'first_name' => 'required|max:50|min:3',
            'last_name' => 'required|max:50|min:3',
//            'phone' => 'required|min:3|unique:users',
            'email' => 'required|max:50|email|unique:users',
            'password'  => 'required|min:6',
            'repeat_password' => 'required|same:password',
        ]);

        $data = $request->all();
        $role = Role::where('name', 'user')->first();
        $data['password'] = bcrypt($data['password']);
        $user = User::create($data);

        $user->attachRole($role);
        // login user
        try {

            return $this->successResponse('Login successful!', [
                'user' =>  new UserCollection(User::where('email', $user->email)->first()),
                'token' => $user->createToken($request->email)->plainTextToken
            ]);
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return $this->errorResponse('Could not create token', 500);
        }
    }


    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|max:50|email|exists:users',
            'password'  => 'required|min:6'
        ], [
            'exists' => 'This email does not exist.',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }
        // assuming that the email or username is passed through a 'login' parameter
        $login = $request->input('email');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $request->merge([$field => $login]);
        $credentials = $request->only($field, 'password');

        try {

            if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
                $user = User::where('email', $request->email)->first();

                $data['user'] = new UserCollection(User::where('email', $request->email)->first());
                $data['token'] = $user->createToken($request->email)->plainTextToken;
                return $this->successResponse('Login Successful', $data);

            }
            else{
                return $this->errorResponse('Unauthorised.',404);
            }


        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return $this->errorResponse('Unauthorised.',404);

        }

        // return $this->respondWithToken($token);
    }

    public function user()
    {
        $data['user'] = new UserCollection(auth('api')->user());
        return  response()->json($data);
    }

    public function update(Request $request): JsonResponse
    {
        $data = $this->updateData($request);
        $user = user::find(auth('api')->user()->id);
        $user->update($data);
        $user = user::find(auth('api')->user()->id);
        return response()->json($user);
    }

    public function logout(): JsonResponse
    {
        auth('api')->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }


    public function refresh(): JsonResponse
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'user' => $this->guard('api')->user(),
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }

    public function updateOnline(): JsonResponse
    {
        $user = auth('api')->user();
        $user->last_seen = Carbon::now()->addMinutes(5);
        $user->save();
        return response()->json($user);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:6',
        ]);

        if (!(Hash::check($request->get('current_password'), auth()->user()->password))) {
            // The passwords matches
            return response()->json('Your current password does not matches with the password you provided. Please try again.', 500);
        }
        if(strcmp($request->get('current_password'), $request->get('new_password')) == 0){
            //Current password and new password are same
            return response()->json('New Password cannot be same as your current password. Please choose a different password', 500);
        }

        //Change Password
        $user = auth()->user();
        $user->password = bcrypt($request->get('new_password'));
        $user->save();
        return response()->json('success');
    }

    public function guard() {
        return \Auth::Guard('api');
    }

    protected function getData(Request $request)
    {
        $rules = [
            'username' => 'required|max:50|min:3|unique:users',
            'first_name' => 'required|max:50|min:3',
            'last_name' => 'required|max:50|min:3',
            'phone' => 'required|min:3|unique:users',
            'email' => 'required|max:50|email|unique:users',
            'password'  => 'required|min:6',
            'repeatPassword' => 'required|same:password',

        ];
        $data = $request->validate($rules);
        return $data;
    }
    protected function updateData(Request $request)
    {
        $rules = [
            'first_name' => 'required|min:3|max:50',
            'last_name' => 'required|min:3',

        ];
        return $request->validate($rules);
    }

    public function destroy($id)
    {
        $user = User::destroy($id);
        return response()->json($user);
    }

}
