<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class ApiBaseController extends Controller
{
    /**
     * @param mixed $result
     * @param stirng $message
     * @param int $code
     * 
     * @return \Illuminate\Http\Response
     */
    protected function success($result, $message, $code = 200)
    {
        $response['status'] = $code;
        $response['message'] = $message;

        if (array_key_exists('common_data', $result)) {
            $response['common_data'] = $result['common_data'];    
        }
        
        $response['data'] = $result['data'];
        
        return response()->json($response)->setStatusCode($code);
    }

    /**
     * @param string $errors
     * @param int $code
     * 
     * @return \Illuminate\Http\Response
     */
    protected function error($errors, $code = 404)
    {
        $response = [
            'status'  => $code,
            'message' => Response::$statusTexts[$code],
            'errors'  => $errors
        ];

        return response()->json($response)->setStatusCode($code);   
    }
}
