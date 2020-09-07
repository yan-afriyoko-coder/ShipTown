<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @var int
     */
    private $status_code = 200;

    public function respond($message)
    {
        $response = response()->json(
            ["message" => $message],
            $this->getStatusCode()
        );

        return $response->throwResponse();
    }

    public function respondNotAllowed405($message = 'Method not allowed')
    {
        return $this->setStatusCode(405)
            ->respond($message);
    }

    public function respondOK200($message = null)
    {
        return $this->setStatusCode(200)
            ->respond($message);
    }

    public function respondNotFound($message = "Not Found!")
    {
        return $this->setStatusCode(404)
            ->respond($message);
    }

    public function respondBadRequest($message = "Bad request")
    {
        return $this->setStatusCode(400)
            ->respond($message);
    }

    public function setStatusCode($code)
    {
        $this->status_code = $code;
        return $this;
    }

    public function getStatusCode()
    {
        return $this->status_code;
    }
}
