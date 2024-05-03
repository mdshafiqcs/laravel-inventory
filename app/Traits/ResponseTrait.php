<?php 

namespace App\Traits;

use App\Enum\Status;
use Illuminate\Http\Response;

trait ResponseTrait {

    public function errorResponse( string $message, int $code = 400, Status $status = null) : Response {
        return response([
            "message" => $message,
            "status" => $status != null ? $status->value : Status::FAILED->value, 
        ], $code);
    }

    public function successResponse($data, string $message = null, Status $status = null, int $code = 200) : Response {
        return response([
            "message" => $message ?? "",
            "status" => $status != null ? $status->value : Status::SUCCESS->value, 
            "data" => $data,
        ], $code);
    }

}