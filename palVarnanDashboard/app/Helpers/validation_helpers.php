<?php

use Illuminate\Support\Facades\Response;

function successResponse($message)
{
    return Response::json([
        "status" => "success",
        "message" => $message
    ]);
}


function successResponseWithData($message, $data)
{
    return Response::json([
        "status" => "success",
        "message" => $message,
        "data" => $data
    ]);
}


function errorResponse($message, $statusCode = 500)
{
    return Response::json([
        "status" => "error",
        "message" => $message
    ], $statusCode);
}
