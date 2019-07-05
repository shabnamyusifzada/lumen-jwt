<?php
/**
 * Created by PhpStorm.
 * User: Shabnam
 * Date: 25.06.2019
 * Time: 11:02
 */

namespace App\Http\Controllers;


use App\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{

    public function getCountry()
    {
        $countries = Country::where('parent_id', '0')->get();
        return view('country')->with('countries', $countries);
    }

    public function postCountry(Request $request)
    {
        Country::create($request->all());
        return redirect()->to('api/country');
    }

    /**
     * @OA\Get(
     *     path="/api/cities/{country_id}",
     *     tags={"Cities"},
     *     operationId="getCities",
     *     summary="Get cities of user by country ID ",
     *     description="",
     *     @OA\Parameter(
     *         description="ID of country",
     *         in="path",
     *         name="country_id",
     *         required=true,
     *         @OA\Schema(
     *           type="integer",
     *           format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cities",
     *         @OA\JsonContent(ref="#/components/schemas/Country")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Error",
     *     ),
     *    @OA\Response(
     *         response=405,
     *         description="Method Not Allowed",
     *     ),
     *     security={
     *       {"bearerAuth": {}}
     *     }
     * )
     */

    public function listCountries($id)
    {
        $data = [];

        $branch = array();
        $branch['id'] = $id;
        $children = Country::where('parent_id', $id)->get();
        if (count($children) > 0) {
            $branch['city'] = $this->getChildren($children);
        } else {
            $branch['city'] = array();

        }
        $data[] = $branch;

        return response()->json(['success' => true, 'response' => $data], 200);
    }

    public function getChildren($children)
    {
        foreach ($children as $child) {
            $child_branch = array();
            $child_branch['id'] = $child->id;
            $child_branch['name'] = $child->name;
            $children = Country::where('parent_id', $child->id)->get();
            if (count($children) > 0) {
                $child_branch['city'] = $this->getChildren($children);
            } else {
                $child_branch['city'] = array();
            }
            return $child_branch;

        }

    }
}