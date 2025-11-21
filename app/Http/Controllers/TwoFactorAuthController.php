<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FAQRCode\Google2FA;

class TwoFactorAuthController extends Controller
{
    /**
     * Show 2FA setup form
     */
    public function showSetupForm()
    {
        $user = Auth::user();

        // If 2FA is already enabled, redirect to dashboard
        if ($user->google2fa_enabled) {
            return redirect()->route('dashboard.unified')->with('info', '2FA is already enabled.');
        }

        // Generate secret if not exists
        if (!$user->google2fa_secret) {
            $user->generate2faSecret();
        }

        $google2fa = new Google2FA();

        // Generate QR code image
        $qrCode = $google2fa->getQRCodeInline(
            config('app.name', 'Laravel'),
            $user->email,
            $user->google2fa_secret
        );

        $secret = $user->google2fa_secret;

        return view('auth.2fa-setup', compact('qrCode', 'secret'));
    }

    /**
     * Enable 2FA
     */
    public function enable(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = Auth::user();

        if ($user->verify2fa($request->code)) {
            $user->enable2fa();

            // Generate backup codes
            $backupCodes = $user->backup_codes;

            return redirect()->route('2fa.backup-codes')
                ->with('success', 'Two-factor authentication has been enabled successfully!')
                ->with('backupCodes', $backupCodes);
        }

        return redirect()->back()->with('error', 'Invalid verification code. Please try again.');
    }

    /**
     * Show backup codes
     */
    public function showBackupCodes()
    {
        $user = Auth::user();
        $backupCodes = session('backupCodes', $user->backup_codes);

        if (!$backupCodes) {
            return redirect()->route('dashboard.unified');
        }
        $notifications = $this->getNotifications();

        return view('auth.2fa-backup-codes', compact('backupCodes', 'notifications'));
    }

    /**
     * Download backup codes as TXT
     */
    public function downloadBackupCodes()
    {
        $user = Auth::user();
        $backupCodes = $user->backup_codes;

        if (!$backupCodes) {
            return redirect()->back()->with('error', 'No backup codes available.');
        }

        $content = "Backup Codes for " . config('app.name', 'Laravel') . "\n";
        $content .= "Generated on: " . now()->format('Y-m-d H:i:s') . "\n\n";
        $content .= "IMPORTANT: Store these codes in a secure place. Each code can be used once.\n\n";

        foreach ($backupCodes as $code) {
            $content .= $code . "\n";
        }

        return response($content)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="backup-codes-' . $user->id . '.txt"');
    }

    /**
     * Regenerate backup codes
     */
    public function regenerateBackupCodes()
    {
        $user = Auth::user();
        $backupCodes = $user->generateBackupCodes();

        return redirect()->route('2fa.backup-codes')
            ->with('success', 'Backup codes regenerated successfully!')
            ->with('backupCodes', $backupCodes);
    }

    /**
     * Disable 2FA
     */
    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user = Auth::user();
        $user->disable2fa();

        return redirect()->route('profile.show')
            ->with('success', 'Two-factor authentication has been disabled.');
    }
}
