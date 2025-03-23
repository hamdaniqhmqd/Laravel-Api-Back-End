<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResponseApiResource extends JsonResource
{
    //define properti
    public $status;
    public $message;
    public $resource;
    public $errors;

    /**
     * __construct
     *
     * @param  mixed $status
     * @param  mixed $message
     * @param  mixed $resource
     * @param  mixed $errors
     * @return void
     */
    public function __construct($status, $message, $resource, $errors)
    {
        parent::__construct($resource);
        $this->status  = $status;
        $this->message = $message;
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
            'errors'      => $this->errors,
        ];
    }
}
