<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\Post\User as PostUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;


class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => '❌ Email not found.']);
        }

        $otp = rand(100000, 999999);
        DB::table('password_resets_custom')->updateOrInsert(
            ['email' => $request->email],
            ['otp' => $otp, 'created_at' => Carbon::now()]
        );

        Mail::to($request->email)->send(new OtpMail($otp));

        return redirect()->route('password.otp.verify')->with('email', $request->email);
    }

    public function showOtpForm()
    {
        return view('auth.passwords.otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6'
        ]);

        $record = DB::table('password_resets_custom')
            ->where('email', $request->email)
            ->first();

        if (!$record) {
            return back()->withErrors(['otp' => '❌ Invalid OTP']);
        }

        // Expiry check
        $otpTime = \Carbon\Carbon::parse($record->created_at);
        if ($otpTime->diffInMinutes(now()) > 10) {
            DB::table('password_resets_custom')
                ->where('email', $request->email)
                ->update(['used' => true]);
            return back()->withErrors(['otp' => '❌ OTP expired. Please request a new one.']);
        }

        //  OTP must match check
        if ($record->otp !== $request->otp) {
            return back()->withErrors(['otp' => '❌ Invalid OTP']);
        }

        // OTP is valid, mark it used
        DB::table('password_resets_custom')
            ->where('email', $request->email)
            ->update(['used' => true]);

        session(['verified_email' => $request->email]);
        return redirect()->route('password.custom.reset');
    }






    public function showCustomResetForm()
    {
        if (!session('verified_email')) {
            return redirect()->route('password.request');
        }

        return view('auth.passwords.reset-custom');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed'
        ]);

        $user = User::where('email', session('verified_email'))->first();
        $user->update(['password' => bcrypt($request->password)]);

        DB::table('password_resets_custom')->where('email', session('verified_email'))->delete();
        session()->forget('verified_email');

        return redirect()->route('login')->with('status', 'Password reset successfully!');
    }
}
