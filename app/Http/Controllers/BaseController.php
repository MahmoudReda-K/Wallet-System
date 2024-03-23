<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;

class BaseController extends Controller
{
    protected function successResponse($data, $code = Response::HTTP_OK, $message = null)
    {
        $response = [
            'data' => $data,
            'message' => $message,
        ];

        return response()->json($response, $code);
    }

    protected function successMessage($code = Response::HTTP_OK, $message = null)
    {
        $response = [
            'message' => $message,
        ];

        return response()->json($response, $code);
    }

    protected function errorResponse($message, $code)
    {
        $response = [
            'data' => null,
            'message' => $message,
        ];

        return response()->json($response, $code);
    }
}
