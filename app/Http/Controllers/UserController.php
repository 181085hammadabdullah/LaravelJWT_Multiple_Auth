<?php

    namespace App\Http\Controllers;

    use App\User;
    use App\Admin;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\Validator;
    use JWTAuth;
    use Tymon\JWTAuth\Exceptions\JWTException;
    use Config;
    use DB;
    class UserController extends Controller
    {

        function __construct()
        {
            Config::set('auth.providers.users.model', \App\User::class);
        }
        public function authenticate(Request $request)
        {
            $credentials = $request->only('email', 'password');

            try {
                if (! $token = JWTAuth::attempt($credentials)) {
                    return response()->json([
                        'Errors'=>"Invalid Email or Password",
                        'Message'=>"CannotLogin User",
                        "status"=>400
                    ]);
                }
            } catch (JWTException $e) {
                return response()->json(['error' => 'could_not_create_token'], 500);
            }
            return response()->json(compact('token'));
        }

        public function register(Request $request)
        {
                $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]);

            if($validator->fails()){
                    return response()->json([
                        'Errors'=>$validator->errors(),
                        'Message'=>"Cannot Register User",
                        "status"=>400
                    ]);
            }

            $user = User::create([
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'password' => Hash::make($request->get('password')),
            ]);

            $token = JWTAuth::fromUser($user);

            return response()->json(compact('user','token'),201);
        }

        public function getAuthenticatedUser()
            {
                    try {

                            if (! $user = JWTAuth::parseToken()->authenticate()) {
                                    return response()->json(['user_not_found'], 404);
                            }

                    } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
                            return response()->json([
                                'Error'=>'Token Expired',
                                'Status'=>$e->getStatusCode()
                            ]);

                    } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

                        return response()->json([
                            'Error'=>'Invalid Token',
                            'Status'=>$e->getStatusCode()
                        ]);

                    } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

                        return response()->json([
                            'Error'=>'Token Absent',
                            'Status'=>$e->getStatusCode()
                        ]);

                    }

                    return response()->json(compact('user'));
            }





            public function adminLogin(Request $request){
                Config::set('jwt.user', 'App\Admin'); 
                Config::set('auth.providers.users.model', \App\Admin::class);
                $credentials = $request->only('email', 'password');
                $token = null;
                try {
                    if (!$token = JWTAuth::attempt($credentials)) {
                        return response()->json([
                            'response' => 'error',
                            'message' => 'invalid_email_or_password',
                        ]);
                    }
                } catch (JWTAuthException $e) {
                    return response()->json([
                        'response' => 'error',
                        'message' => 'failed_to_create_token',
                    ]);
                }
                return response()->json([
                    'response' => 'success',
                    'result' => [
                        'token' => $token,
                        'message' => 'I am Admin user',
                    ],
                ]);
            }
    }
