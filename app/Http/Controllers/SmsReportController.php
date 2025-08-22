<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class SmsReportController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    public function index(Request $request)
    {
        if (!session()->has('firebase_user_email')) {
            return redirect()->route('login')
                ->with('error', 'You must be logged in to view this page.');
        }

        $email  = strtolower(session('firebase_user_email'));
        $prefix = match ($email) {
            'canocotan123@gmail.com'  => 'Canocotan',
            'lafilipina123@gmail.com' => 'LaFilipina',
            'mabini123@gmail.com'     => 'Mabini',
            default                   => null,
        };

        // Safely pass empty arrays so the incident blade doesn’t error
        $fireReports = [];
        $otherEmergencyReports = [];

        if (!$prefix) {
            return view('ADMIN-DASHBOARD.incident-reports', compact('fireReports', 'otherEmergencyReports') + [
                'smsReports' => [],
            ]);
        }

        try {
            $raw = $this->firebase->getSmsReports($prefix) ?? [];

            // $raw may be an assoc array keyed by Firebase ID; normalize to a list and inject id
            $smsReports = collect($raw)
                ->map(function ($row, $key) {
                    $row = is_array($row) ? $row : [];
                    $row['id'] = $row['id'] ?? (is_string($key) ? $key : null);
                    return $row;
                })
                // don’t drop rows without date/time — we’ll sort with a fallback
                ->sortByDesc(function ($r) {
                    return $this->bestTimestamp(
                        Arr::get($r, 'date'),
                        Arr::get($r, 'time'),
                        Arr::get($r, 'timestamp') ?? Arr::get($r, 'createdAt') ?? Arr::get($r, 'updatedAt')
                    );
                })
                ->values()
                ->all();

        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Unable to fetch SMS reports. Please try again.');
        }

        // IMPORTANT: return the blade that actually contains the SMS section you showed
        return view('ADMIN-DASHBOARD.incident-reports', compact('fireReports', 'otherEmergencyReports', 'smsReports'));
    }

    /**
     * Build the best possible timestamp for sorting:
     * - If date+time exist (dd/mm/yyyy or yyyy-mm-dd; time may be 12h or 24h), parse them
     * - Else fall back to numeric `timestamp|createdAt|updatedAt`
     */
    private function bestTimestamp(?string $date, ?string $time, $fallback): int
    {
        $ts = $this->parseDmyTimeSafe($date, $time);
        if ($ts > 0) return $ts;

        // accept numeric or numeric-string fallbacks
        if (is_numeric($fallback)) return (int)$fallback;

        // last resort
        return 0;
    }

    /**
     * Parse “dd/mm/yyyy HH:MM” (or dd-mm-yyyy; or yyyy-mm-dd) + optional AM/PM safely.
     */
    private function parseDmyTimeSafe(?string $d, ?string $t): int
    {
        $d = trim((string)$d);
        $t = trim((string)$t);

        if ($d === '' && $t === '') return 0;

        // normalize separators
        $d = str_replace('-', '/', $d);

        // dd/mm/yyyy or dd/mm/yy
        $p = explode('/', $d);
        if (count($p) === 3 && strlen($p[0]) <= 2) {
            [$day, $month, $year] = $p;
            if (strlen($year) === 2) $year = '20' . $year;

            // normalize time (let strtotime handle AM/PM too)
            $iso = sprintf('%04d-%02d-%02d %s', (int)$year, (int)$month, (int)$day, $t ?: '00:00');
            return strtotime($iso) ?: 0;
        }

        // maybe already yyyy/mm/dd or yyyy-mm-dd (we replaced - with / above)
        if (count($p) === 3 && strlen($p[0]) === 4) {
            [$year, $month, $day] = $p;
            $iso = sprintf('%04d-%02d-%02d %s', (int)$year, (int)$month, (int)$day, $t ?: '00:00');
            return strtotime($iso) ?: 0;
        }

        // fallback: let strtotime try
        return strtotime(trim($d . ' ' . $t)) ?: 0;
    }

    public function updateSmsReportStatus(Request $request, $id)
    {
        $status = $request->input('status');

        $email  = strtolower(session('firebase_user_email'));
        $prefix = match ($email) {
            'canocotan123@gmail.com'  => 'Canocotan',
            'lafilipina123@gmail.com' => 'LaFilipina',
            'mabini123@gmail.com'     => 'Mabini',
            default                   => null,
        };

        if (!$prefix) {
            return redirect()->route('sms-reports')->with('error', 'No matching fire station found.');
        }

        try {
            $this->firebase->updateReportStatus($prefix, 'SmsReport', $id, $status);
            return redirect()->route('sms-reports')->with('success', 'SMS report status updated successfully.');
        } catch (\Throwable $e) {
            return redirect()->route('sms-reports')->with('error', 'Failed to update SMS report status.');
        }
    }
}
