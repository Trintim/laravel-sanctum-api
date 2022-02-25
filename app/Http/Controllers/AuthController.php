<?php

namespace App\Http\Controllers;

use App\Http\Library\ApiHelpers;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiHelpers;

    public function register(Request $request)
    {

        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'email',
            'password' => 'required|min:6|string|confirmed'
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => Hash::make($fields['password'])
        ]);

        $response = [
            'user' => $user,
        ];
        return response($response, 201);
    }

    public function login(Request $request)
    {

        $fields = $request->validate([
            'email' => 'email',
            'password' => 'required|string|confirmed'
        ]);

        $user = User::where('email', $fields['email'])->first();
        //dd($user);
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response(['message' => 'Bad Creds'], 401);
        }

        if ($user && $user->role === 1) {
            $token = $user->createToken('auth_token', ['admin'])->plainTextToken;
        } else if ($user && $user->role === 2) {
            $token = $user->createToken('auth_token', ['subAdmin'])->plainTextToken;
        } else if ($user && $user->role === 3) {
            $token = $user->createToken('auth_token', ['associates'])->plainTextToken;
        } else {
            return response()->json('not permissions', 401);
        }

        $response = [
            'user' => $user,
            'token' => $token
        ];
        return response($response, 201);
    }

    public function logout(Request $request)
    {

        if (Auth::user()->role === 1) {
            auth()->user()->tokens()->delete();
        } else if (Auth::user()->role === 2) {
            auth()->user()->tokens()->delete();
        } else if (Auth::user()->role === 3) {
            auth()->user()->tokens()->delete();
        }
        return [
            'message' => 'Logged Out'
        ];
    }

    public function createSubAdmin(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($this->isAdmin($user)) {
            $validator = Validator::make($request->all(), $this->userValidatedRules());
            if ($validator->passes()) {
                $user = User::create([
                    'name' => $request->input('name'),
                    'email' => $request->input('email'),
                    'role' => 2,
                    'password' => Hash::make($request->input('password'))
                ]);

                //$subAdminToken = $user->createToken('auth_token', ['subAdmin'])->plainTextToken;
                return $this->onSuccess($user, 'User created with Sub-Admin Privilege');
            }
            return $this->onError(400, $validator->errors());
        }

        return $this->onError(401, 'Unauthorized Access');
    }

    public function createAssociates(Request $request): JsonResponse
    {
        $user = $request->user();
        //dd($user);

        if ($this->isAdmin($user)) {
            $validator = Validator::make($request->all(), $this->userValidatedRules());
            if ($validator->passes()) {
                $user = User::create([
                    'name' => $request->input('name'),
                    'email' => $request->input('email'),
                    'role' => 3,
                    'password' => Hash::make($request->input('password')),
                ]);
                //$associatesToken = $user->createToken('auth_token', ['associates'])->plainTextToken;
                return $this->onSuccess($user, 'User created with Associates Privilege');
            }
            return $this->onError(400, $validator->errors());
        }
        return $this->onError(401, 'Unauthorized Access');
    }

    public function deleteUser(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        if ($this->isAdmin($user)) {
            $user = User::find($id);
            if ($user->role !== 1) {
                $user->delete();
                $user->tokens()->delete();
                if (!empty($user)) {
                    return $this->onSuccess(200, 'User Deleted');
                }
                return $this->onError(404, 'User not Fount');
            }
        }
        return $this->onError(401, 'Unauthorized Access');
    }

    public function updateUser(Request $request, $id)
    {

        $user = $request->user();
        if ($this->isAdmin($user)) {
            $user = User::find($id);
            if ($user->role === 1 && $user->id == $id) {
                $user->name = $request->input('name');
                $user->email = $request->input('email');
                $user->password = Hash::make($request->input('password'));
                $user->update();
               
                return $this->onSuccess($user, 'User Updated');
            }
        }

        else if ($this->isSubAdmin($user)) {
            //dd($user);
            $user = User::find($id);
            //dd($user);
            if ($user->role === 2 && $user->id == $id) {
                //dd($user);
                $user->name = $request->input('name');
                $user->email = $request->input('email');
                $user->password = Hash::make($request->input('password'));
                $user->update();
               
                return $this->onSuccess($user, 'User Updated');
            }
        }

        else if ($this->isAssociates($user)) {
            $user = User::find($id);
            if ($user->role === 3 && $user->id == $id) {
                $user->name = $request->input('name');
                $user->email = $request->input('email');
                $user->password = Hash::make($request->input('password'));
                $user->update();
               
                return $this->onSuccess($user, 'User Updated');
            }
        }
        return $this->onError(401, 'Unauthorized Access');
        //$user = $request->user();
        //dd($user);
        /* $user = User::find($id);
        dd($user);
        $validator = Validator::make($request->all(), $this->userValidatedRules());
        if ($validator->passes()) {
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->password = Hash::make($request->input('password'));
            $user->update();

            //$associatesToken = $user->createToken('auth_token', ['associates'])->plainTextToken;
            return $this->onSuccess($user, 'User update with success');
        }
        return $this->onError(400, $validator->errors()); */
    }
}
