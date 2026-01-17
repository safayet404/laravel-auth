<?php

namespace App\Traits;

trait ApiResponse
{
    protected function success($data, string $message = "Operation Successfull", int $code = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data

        ],$data);
    }

    protected function error(string $message)
    {
        return response()->json([
            'status' => 'failed',
            'message' => $message
        ]);
    }
}
