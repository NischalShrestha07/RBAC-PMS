<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class TwoFactorVerificationController extends Controller
{
    /**
     * Show 2FA verification form
     */
    public function showVerificationForm()
    {
        $user = Auth::user();

        // If 2FA is not enabled or already verified, redirect to dashboard
        if (!$user->google2fa_enabled || Session::get('2fa_verified')) {
            return redirect()->intended(route('dashboard.unified'));
        }

        return view('auth.2fa-verify');
    }

    /**
     * Verify 2FA code
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);
        // return $request;
        $user = Auth::user();

        // Check if it's a backup code
        if ($user->useBackupCode($request->code)) {
            Session::put('2fa_verified', true);
            Session::put('2fa_backup_used', true);

            return $this->redirectToIntended()
                ->with('warning', 'You have used a backup code. Please generate new backup codes.');
        }

        // Verify regular 2FA code
        if ($user->verify2fa($request->code)) {
            Session::put('2fa_verified', true);
            Session::forget('2fa_backup_used');

            return $this->redirectToIntended()
                ->with('success', 'Successfully verified!');
        }

        return redirect()->back()->with('error', 'Invalid verification code. Please try again.');
    }

    /**
     * Redirect to intended URL after 2FA verification
     */
    private function redirectToIntended()
    {
        $intendedUrl = Session::get('2fa_intended_url', route('dashboard.unified'));
        Session::forget('2fa_intended_url');

        return redirect()->to($intendedUrl);
    }

    /**
     * Resend 2FA notification (if you implement email/SMS 2FA later)
     */
    public function resend()
    {
        return redirect()->back()->with('info', 'Please enter the code from your authenticator app.');
    }
}
