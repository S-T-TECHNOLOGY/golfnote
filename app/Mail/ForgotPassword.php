<?php


namespace App\Mail;


use Carbon\Carbon;
use Illuminate\Mail\Mailable;

class ForgotPassword extends Mailable
{
    private $email;
    private $password;
    public function __construct($email, $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    public function build()
    {
        $date = (string)Carbon::now('UTC +7');
        $title = 'Quên mật khẩu';

        return  $this->view('emails.forgot_password')
            ->subject($title . ' ' . $date )
            ->to($this->email)
            ->with([
                'password' => $this->password,
            ]);
    }
}