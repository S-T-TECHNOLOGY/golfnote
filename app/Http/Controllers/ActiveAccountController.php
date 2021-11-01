<?php


namespace App\Http\Controllers;


use App\Constants\ActiveStatus;
use App\Models\MailOtp;
use App\Models\User;
use Illuminate\Http\Request;


class ActiveAccountController extends Controller
{
    public function activate(Request $request)
    {
        $code = $request->code;
        $mailOtp = MailOtp::where('code', $code)->where('verified', ActiveStatus::INACTIVE)->first();
        if ($mailOtp) {
            $user = User::find($mailOtp->user_id);
            $user->active = ActiveStatus::ACTIVE;
            $user->save();
            $mailOtp->verified = ActiveStatus::ACTIVE;
            $mailOtp->save();
        }
        return view('users.active');
    }
}