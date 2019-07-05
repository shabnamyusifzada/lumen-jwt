<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;


/**
 * @OA\Info(title="My API", version="0.1"),
 * @OA\SecurityScheme(
 *      securityScheme="bearerAuth",
 *      in="header",
 *      name="bearerAuth",
 *      type="http",
 *      scheme="bearer",
 *      bearerFormat="JWT",
 * ),
 * @OA\Tag(
 *     name="Auth",
 *     description="Auth endpoints",
 * ),
 * @OA\Tag(
 *     name="Users",
 *     description="Users endpoints",
 * ),
 * @OA\Tag(
 *     name="Cities",
 *     description="Cities endpoints",
 * ),
 * @OA\Tag(
 *     name="Subcategories",
 *     description="Subcategories endpoints",
 * )
 */
class Controller extends BaseController
{
    //
}
