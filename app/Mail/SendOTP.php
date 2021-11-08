<?php


namespace App\Mail;


use App\Constants\MailOtpType;
use App\Definitions\OTPType;
use Carbon\Carbon;
use Illuminate\Mail\Mailable;

class SendOTP extends Mailable
{
    private $email;
    private $code;
    private $type;
    public function __construct($email, $code, $type)
    {
        $this->email = $email;
        $this->code = $code;
        $this->type = $type;
    }

    public function build()
    {
        $date = (string)Carbon::now('UTC +7');
        $title = '';
        $content = '';
        switch ($this->type) {
            case MailOtpType::TYPE_REGISTER:
                $title = 'Kích hoạt tài khoản';
                $header = 'Chào mừng bạn đến với GoflNote';
                $content = 'Nhấp vào liên kết bên dưới để kích hoạt tài khoản của bạn';
                break;


        }
        return  $this->view('emails.otp')
            ->subject($title . ' ' . $date )
            ->to($this->email)
            ->with([
                'code' => $this->code,
                'header' => $header,
                'content'  => $content,
            ]);
    }
}