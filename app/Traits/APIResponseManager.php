<?php

namespace App\Traits;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

trait APIResponseManager {

    public function responseManager($statusCode, $message, $data=null) {

        $apiReponse = [
            'statusCode' => $statusCode, 
            'message'=>$message, 
            'data'=>$data
        ];
        Log::info('API-INFO :'.$message);
        return response()->json($apiReponse);
        
    }

    public function validationResponse($e) {

        $errors = $e->validator->errors();
        $col = new Collection($errors);
        Log::error('API-VALIDATION-ERROR :'.$col);
        return $col;

    }
}
