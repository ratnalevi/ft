<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *      version="2.0.1",
 *      title="Floteq API",
 *      description="Floteq API OpenAPI description",
 *      @OA\Contact(
 *          email="levi.ratnakar@flotequsa.com"
 *      )
 * )
 * @OA\Server(
 *      url="https://devweb01.flotequsa.com/api",
 *      description="Development server",
 * @OA\Contact(
 *      email="levi.ratnakar@flotequsa.com"
 *  )
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests;
    use ValidatesRequests;
}
