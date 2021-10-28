<?php


namespace App\Mail;


use App\Definitions\OTPType;
use Carbon\Carbon;
use Illuminate\Mail\Mailable;

class SendOTP extends Mailable
{
    private $email;
    private $otp;
    private $type;
    public function __construct($email, $otp, $type)
    {
        $this->email = $email;
        $this->otp = $otp;
        $this->type = $type;
    }

    public function build()
    {
        $date = (string)Carbon::now('UTC +7');
        $title = '';
        $content = '';
        switch ($this->type) {
            case OTPType::TYPE_REGISTER:
                $title = 'OTP Đăng ký';
                $content = 'Bạn đã đăng ký thành công, đây là mã kích hoạt tài khoản của bạn :';
                break;
            case OTPType::TYPE_FORGOT_PASSWORD:
                $title = 'OTP Quên mật khẩu';
                $content = 'Bạn dùng mã này để lấy lại mật khẩu :';
                break;
            case OTPType::TYPE_UPDATE_PROFILE:
                $title = 'OTP cập nhập email';
                $content = 'Bạn dùng mã này để cập nhật email :';
                break;


        }
        return  $this->view('emails.otp')
            ->subject($title . ' ' . $date )
            ->to($this->email)
            ->with([
                'otp' => $this->otp,
                'title' => $title,
                'content'  => $content,
            ]);
    }
}