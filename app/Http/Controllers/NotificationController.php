<?php

namespace App\Http\Controllers;

use App\Models\Installment;
use App\Models\PdcInstallment;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = $this->getNotifications();

        return view('parts.superNotification', compact('notifications'));
    }
    public function electronicsPartsNotifications()
    {
        $notifications = $this->getNotifications();

        return view('parts.electronicsNotification', compact('notifications'));
    }
    public function electricalPartsNotifications()
    {
        $notifications = $this->getNotifications();

        return view('parts.electricalNotification', compact('notifications'));
    }

    public function loadMoreNotifications(Request $request)
    {
        $type = $request->input('type'); // 'pdc' or 'commitment'
        $offset = $request->input('offset', 0);
        $limit = 10; // Number of notifications to load per request

        $notifications = $this->getNotifications($type, $offset, $limit);

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'hasMore' => count($notifications) >= $limit
        ]);
    }

    public function markNotificationAsViewed(Request $request)
    {
        $type = $request->input('type'); // 'pdc' or 'commitment'
        $installmentId = $request->input('installment_id');

        if ($type === 'pdc') {
            PdcInstallment::where('id', $installmentId)->update(['viewed' => true]);
        } elseif ($type === 'commitment') {
            Installment::where('id', $installmentId)->update(['viewed' => true]);
        }

        return response()->json(['success' => true]);
    }
}
