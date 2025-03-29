<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthApiResource extends JsonResource
{
    //define properti
    public $status;
    public $message;
    public $resource;
    public $token;
    public $errors;

    /**
     * __construct
     *
     * @param  mixed $status
     * @param  mixed $message
     * @param  mixed $resource
     * @param  mixed $token
     * @param  mixed $errors
     * @return void
     */
    public function __construct($status, $message, $resource, $token, $errors)
    {
        parent::__construct($resource);
        $this->status  = $status;
        $this->message = $message;
        $this->token = $token;
        $this->errors = $errors;
    }

    /**
     * toArray
     *
     * @param  mixed $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'success'   => $this->status,
            'message'   => $this->message,
            'data'      => $this->resource,
            'token'      => $this->token,
            'errors'      => $this->errors,
        ];
    }
}
