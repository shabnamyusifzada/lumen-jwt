<?php

namespace App\Http\Controllers;

use App\Category;
use App\Code;
use App\Country;
use App\Twilio\Messages\VerifyPhone;
use App\User;
use Carbon\Carbon;
use Faker\Provider\Image;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/register",
     *     tags={"Users"},
     *     operationId="createUser",
     *     summary="Create a new user",
     *     description="",
     *     @OA\RequestBody(
     *         description="User object that needs to be created",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/User"),
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                  @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="username",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="date_of_birth",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="salary_min",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="phone",
     *                     type="string"
     *                 ),
     *               ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     ),
     *     @OA\Response(response=422, description="Invalid Input"),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Error",
     *     ),
     *    @OA\Response(
     *         response=405,
     *         description="Method Not Allowed",
     *     ),
     * )
     */

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users',
            'username' => 'required',
            'date_of_birth' => 'date_format:"d-m-Y|required',
            'salary_min' => 'numeric|required',
            'phone' => 'required|min:10|numeric',
            'password' => 'required|min:8',
            'category' => 'required',
            'subcategory' => 'required',
            'wished_country' => 'required',
            'wished_city' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['response' => 'Sahələr doğru deyil', 'success' => false], 422);
        }

        $wished_country = $request->input('wished_country');
        $wished_city = $request->input('wished_city');
        $category_input = $request->input('category');
        $subcategory_input = $request->input('subcategory');

        $birthday = strtotime($request->input('date_of_birth'));
        $request->merge(['date_of_birth' => $birthday]);
        $password = $request->password;
        $password_hash = Hash::make($password);
        $request->merge(['password' => $password_hash]);
        $user = User::create($request->all());

        $user->category()->attach($category_input);
        $user->category()->attach($subcategory_input);

        $user->country()->attach($wished_country);
        $user->country()->attach($wished_city);

        return response()->json(['response' => 'Müvəffəqiyyətlə əlavə edildi', 'success' => true, 'data' => $user], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/forgotpassword/{id}",
     *     tags={"Users"},
     *     operationId="forgotPassword",
     *     summary="Send code by sms",
     *     description="",
     *     @OA\Parameter(
     *         description="ID of user",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *           type="integer",
     *           format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Phone number where the code will be sent",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/User"),
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                  @OA\Property(
     *                     property="phone",
     *                     type="string"
     *                 ),
     *               ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Code is sent successfully",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     ),
     *     @OA\Response(response=422, description="Invalid Input"),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Error",
     *     ),
     *     @OA\Response(
     *         response=405,
     *         description="Method Not Allowed",
     *     ),
     *     security={
     *       {"bearerAuth": {}}
     *     }
     * )
     */


    public function forgotPassword(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|min:10|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json(['response' => 'Sahə doğru deyil', 'success' => false], 422);
        }

        $phone = $request->phone;


        send_message(new VerifyPhone($id, "+994".$phone));

        return response()->json(['response' => 'SMS-Ə BAXIB kodu yazın', 'success' => true], 200);
    }


    /**
     * @OA\Post(
     *     path="/api/changepassword/{id}",
     *     tags={"Users"},
     *     operationId="changePassword",
     *     summary="Change password by sended code",
     *     description="",
     *     @OA\Parameter(
     *         description="ID of user",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *           type="integer",
     *           format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Write sended sms code and generate new password",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/User"),
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                  @OA\Property(
     *                     property="sms_code",
     *                     type="string"
     *                  ),
     *                  @OA\Property(
     *                     property="new_pass",
     *                     type="string"
     *                  ),
     *                  @OA\Property(
     *                     property="new_pass_repeat",
     *                     type="string"
     *                  ),
     *               ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password is successfully changed",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     ),
     *     @OA\Response(response=422, description="Invalid Input"),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Error",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden. Invalid code sent by sms",
     *     ),
     *     @OA\Response(
     *         response=405,
     *         description="Method Not Allowed",
     *     ),
     *     security={
     *       {"bearerAuth": {}}
     *     }
     * )
     */

    public function changePassword(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'sms_code' => 'required|min:4',
            'new_pass' => 'required|min:8',
            'new_pass_repeat' => 'required|min:8|same:new_pass'
        ]);

        if ($validator->fails()) {
            return response()->json(['response' => 'Sahələr doğru deyil', 'success' => false], 422);
        }
        $code_validator = Validator::make($request->all(), [
            'sms_code' => [Rule::exists('sms_code', 'sms_code')->where(function ($query) use ($id) {
                $query->where('user_id', $id);
            })],
        ]);

        if ($code_validator->fails()) {
            return response()->json(['response' => 'Daxil etdiyiniz kod yanlışdır', 'success' => false], 422);
        }
        $new_password = $request->new_pass;
        $password_hash = Hash::make($new_password);

        User::where('id', $id)->update(['password' => $password_hash]);
        Code::where('user_id', $id)->delete();

        return response()->json(['response' => 'Şifrəniz uğurla yeniləndi', 'success' => true], 200);
    }


    /**
     * @OA\GET(
     *     path="/api/profile/{user_id}",
     *     tags={"Users"},
     *     operationId="getProfile",
     *     summary="Get all data of user by user ID ",
     *     description="",
     *     @OA\Parameter(
     *         description="ID of user",
     *         in="path",
     *         name="user_id",
     *         required=true,
     *         @OA\Schema(
     *           type="integer",
     *           format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User Profile",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Error",
     *     ),
     *     @OA\Response(
     *         response=405,
     *         description="Method Not Allowed",
     *     ),
     *     security={
     *       {"bearerAuth": {}}
     *     }
     * )
     */
    public function userProfile($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['success' => false, 'response' => 'İstifadəçi tapılmadı'], 404);
        }

        return response()->json(['success' => true, 'response' => 'İstifadəçi məlumatları', 'data' => $user], 200);
    }


    /**
     * @OA\POST(
     *     path="/api/profile/{user_id}",
     *     tags={"Users"},
     *     operationId="changeProfileImage",
     *     summary="Change image of user by user ID ",
     *     description="",
     *     @OA\Parameter(
     *         description="ID of user",
     *         in="path",
     *         name="user_id",
     *         required=true,
     *         @OA\Schema(
     *           type="integer",
     *           format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Enter encoded image",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/User"),
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                  @OA\Property(
     *                     property="image",
     *                     type="string"
     *                  ),
     *               ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User image successfully changed",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid Input",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Error",
     *     ),
     *     @OA\Response(
     *         response=405,
     *         description="Method Not Allowed",
     *     ),
     *     security={
     *       {"bearerAuth": {}}
     *     }
     * )
     */
    public function uploadImage($id, Request $request)
    {

        $user = User::find($id);
        if (!$user) {
            return response()->json(['success' => false, 'response' => 'İstifadəçi tapılmadı'], 404);
        }
        $decoded_image = base64_decode($request->image,true);
        if(!$decoded_image) {
            return response()->json(['success' => false, 'response' => 'Şəkliniz dəyişdirilmədi'], 422);
        }
        $user->update(['image' => $decoded_image]);
        return response()->json(['success' => true, 'response' => 'Şəkliniz uğurla dəyişdirildi'], 200);
    }


}