<?php
/**
 * Created by PhpStorm.
 * User: PC01
 * Date: 10/17/2018
 * Time: 12:38 PM
 */

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller as Controller;

class BaseApiController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result)
    {
        $response = [
            'success' => true,
            'data'    => $result,
        ];

        return response()->json($response, 200);
    }


    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        return response()->json($response, $code);
    }
}