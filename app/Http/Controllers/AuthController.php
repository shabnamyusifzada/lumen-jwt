<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\JWTAuth;

class AuthController extends Controller
{
    /**
     * @var \Tymon\JWTAuth\JWTAuth
     */
    protected $jwt;

    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Auth"},
     *     operationId="AuthUser",
     *     summary="Authorization",
     *     description="Authorization process",
     *     @OA\RequestBody(
     *         description="Authorization details",
     *         @OA\JsonContent(ref="#/components/schemas/User"),
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *         @OA\Schema(
     *                 @OA\Property(
     *                     property="username",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string"
     *                 ),
     *             )
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Access token created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(response=422, description="Invalid Input"),
     *     @OA\Response(response=404, description="User Not Found"),
     *     @OA\Response(response=500, description="Access Token Error"),
     *     @OA\Response(
     *         response=405,
     *         description="Method Not Allowed",
     *     ),
     * )
     */

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username'    => 'required|max:255',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['responseText' => 'Sahələr doğru deyil', 'success' => false], 422);
        }

        try {

            if (! $token = $this->jwt->attempt($request->only('username', 'password'))) {
                return response()->json(['response' => 'User Not Found or Invalid Data', 'success' => false], 404);
            }

        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['response' => 'Token Expired', 'success' => false], 500);

        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['response' => 'Token Invalid', 'success' => false], 500);

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['response' => ['Token Absent' => $e->getMessage()], 'success' => false], 500);

        }

        return response()->json(['response' => compact('token'), 'success' => true],200);
    }
}