<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\Request;

class IncidentReportsController extends Controller
{
    public function __construct(private FirebaseService $firebase) {}

    public function index(Request $request)
    {
        if (!session()->has('firebase_user_email')) {
            return redirect()->route('login')->with('error', 'You must be logged in to view this page.');
        }

        $email  = strtolower(session('firebase_user_email'));
        $prefix = match ($email) {
            'canocotan123@gmail.com'  => 'Canocotan',
            'lafilipina123@gmail.com' => 'LaFilipina',
            'mabini123@gmail.com'     => 'Mabini',
            default                   => null
        };

        if (!$prefix) {
            return view('ADMIN-DASHBOARD.incident-reports', [
                'fireReports'            => [],
                'otherEmergencyReports'  => [],
                'smsReports'             => [],
            ]);
        }

        try {
            $fireReports           = $this->firebase->getFireReports($prefix);
            $otherEmergencyReports = $this->firebase->getOtherEmergencyReports($prefix);
            $smsReports            = $this->firebase->getSmsReports($prefix);

            // Sort newest → oldest using robust timestamp parsing
            $fireReports = collect($fireReports)
                ->sortByDesc(fn ($r) => $this->toTs($r['date'] ?? null, $r['reportTime'] ?? null, $r['timestamp'] ?? 0))
                ->values()->all();

            $otherEmergencyReports = collect($otherEmergencyReports)
                ->sortByDesc(fn ($r) => $this->toTs($r['date'] ?? null, $r['reportTime'] ?? null, $r['timestamp'] ?? 0))
                ->values()->all();

            // SMS uses `date` + `time` (per your DB screenshot)
            $smsReports = collect($smsReports)
                ->sortByDesc(fn ($r) => $this->toTs($r['date'] ?? null, $r['time'] ?? null, $r['timestamp'] ?? 0))
                ->values()->all();

        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Unable to fetch incident reports. Please try again.');
        }

        return view('ADMIN-DASHBOARD.incident-reports', compact(
            'fireReports',
            'otherEmergencyReports',
            'smsReports'
        ));
    }

    /**
     * Turn various date/time formats into a comparable timestamp.
     * Accepts:
     *  - dd/mm/yyyy or dd/mm/yy (with time like HH:MM or HH:MM:SS, optional AM/PM handled in JS later)
     *  - yyyy-mm-dd
     * Falls back to $fallbackTs if parsing fails.
     */
    private function toTs(?string $date, ?string $time, int|string|null $fallbackTs = 0): int
    {
        if (!$date) return (int) $fallbackTs;

        // Normalize dd/mm/yyyy → yyyy-mm-dd
        if (str_contains($date, '/')) {
            [$dd, $mm, $yy] = explode('/', $date) + [null, null, null];
            if ($dd && $mm && $yy) {
                if (strlen($yy) === 2) $yy = '20' . $yy;
                $date = sprintf('%04d-%02d-%02d', (int) $yy, (int) $mm, (int) $dd);
            }
        }
        $time = $time ?: '00:00'; // default if time missing
        $ts = strtotime(trim("$date $time"));

        return $ts !== false ? $ts : (int) $fallbackTs;
    }
}
