<?php


namespace App\Exceptions;


class BusinessException extends \Exception
{
    protected $code;
    protected $message;
    public function __construct($message = "", $code = 0)
    {
       $this->code = $code;
       $this->message = $message;
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return response(['error_code' => $this->code, 'message' => $this->message],400);
    }

}