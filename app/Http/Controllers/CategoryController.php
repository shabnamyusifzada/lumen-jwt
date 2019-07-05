<?php
/**
 * Created by PhpStorm.
 * User: Shabnam
 * Date: 25.06.2019
 * Time: 11:02
 */

namespace App\Http\Controllers;


use App\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    public function getCategory()
    {
        $categories = Category::where('parent_id', '!=','0')->get();
        return view('category')->with('categories', $categories);
    }

    public function postCategory(Request $request)
    {
        Category::create($request->all());
        return redirect()->to('api/category');
    }

    /**
     * @OA\GET(
     *     path="/api/subcategories/{category_id}",
     *     tags={"Subcategories"},
     *     operationId="getSubcategories",
     *     summary="Get subcategories of user by category ID ",
     *     description="",
     *     @OA\Parameter(
     *         description="ID of category",
     *         in="path",
     *         name="category_id",
     *         required=true,
     *         @OA\Schema(
     *           type="integer",
     *           format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Subcategories",
     *         @OA\JsonContent(ref="#/components/schemas/Category")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
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

    public function listCategories($id)
    {
        $data = [];

            $branch = array();
            $branch['id'] = $id;
            $children = Category::where('parent_id', $id)->get();
            if (count($children) > 0) {
                $branch['subcategory'] = $this->getChildren($children);
            } else {
                $branch['subcategory'] = array();
            }
            $data[] = $branch;

            return response()->json(['success' => true, 'response' => $data], 200);
    }

    public function getChildren($children) {
        foreach ($children as $child) {
            $child_branch = array();
            $child_branch['id'] = $child->id;
            $child_branch['name'] = $child->name;
            $children = Category::where('parent_id', $child->id)->get();
            if (count($children) > 0) {
                $child_branch['subcategory'] = $this->getChildren($children);
            } else {
                $child_branch['subcategory'] = array();
            }
            return $child_branch;

        }

    }
}