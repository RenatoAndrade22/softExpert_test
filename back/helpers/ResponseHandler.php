<?php

class ResponseHandler
{
    static function reponse($message, $statusCode, $data = [])
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        
        echo json_encode(['message' => $message, 'data' => $data]);
    }
}
