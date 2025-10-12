<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\Request;

class IncidentReportsController extends Controller
{
    public function __construct(private FirebaseService $firebase) {}

    public function index(Request $request)
    {
        // Require an authenticated session (kept as-is)
        if (!session()->has('firebase_user_email')) {
            return redirect()->route('login')->with('error', 'You must be logged in to view this page.');
        }

        try {
            // Pull reports from TagumCityCentralFireStation/AllReport structure
            $fireReports   = $this->firebase->getFireReports('TagumCityCentral');
            $otherReports  = $this->firebase->getOtherEmergencyReports('TagumCityCentral');
            $emsReports    = $this->firebase->getEmergencyMedicalServicesReports('TagumCityCentral');
            $smsReports    = $this->firebase->getSmsReports('TagumCityCentral');

            // Sort newest → oldest using robust date/time parsing with fallback to embedded timestamp
            $fireReports = collect($fireReports)
                ->sortByDesc(fn ($r) => $this->toTs($r['date'] ?? null, $r['reportTime'] ?? null, $r['timestamp'] ?? 0))
                ->values()->all();

            $otherReports = collect($otherReports)
                ->sortByDesc(fn ($r) => $this->toTs($r['date'] ?? null, $r['reportTime'] ?? null, $r['timestamp'] ?? 0))
                ->values()->all();

            $emsReports = collect($emsReports)
                ->sortByDesc(fn ($r) => $this->toTs($r['date'] ?? null, $r['reportTime'] ?? null, $r['timestamp'] ?? 0))
                ->values()->all();

            $smsReports = collect($smsReports)
                ->sortByDesc(fn ($r) => $this->toTs($r['date'] ?? null, $r['time'] ?? null, $r['timestamp'] ?? 0))
                ->values()->all();

        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Unable to fetch incident reports. Please try again.');
        }

        return view('ADMIN-DASHBOARD.incident-reports', [
            'fireReports'            => $fireReports,
            'otherEmergencyReports'  => $otherReports,
            'emsReports'             => $emsReports,
            'smsReports'             => $smsReports,
        ]);
    }

    /**
     * Turn various date/time formats into a comparable timestamp.
     * Tries Android formats first:
     *  - Fire:          MM/dd/yyyy
     *  - Other/EMS:     MM/dd/yy
     * Also tries:
     *  - dd/MM/yyyy, dd/MM/yy
     *  - yyyy-MM-dd
     * Falls back to strtotime() then $fallbackTs.
     */
    private function toTs(?string $date, ?string $time, int|string|null $fallbackTs = 0): int
    {
        if (!$date) return (int) $fallbackTs;

        $time = $time ?: '00:00';

        $candidates = [
            'm/d/Y H:i:s', 'm/d/Y H:i', 'm/d/Y',
            'm/d/y H:i:s', 'm/d/y H:i', 'm/d/y',
            'd/m/Y H:i:s', 'd/m/Y H:i', 'd/m/Y',
            'd/m/y H:i:s', 'd/m/y H:i', 'd/m/y',
            'Y-m-d H:i:s', 'Y-m-d H:i', 'Y-m-d',
        ];

        foreach ($candidates as $fmt) {
            $dt = \DateTime::createFromFormat($fmt, trim("$date $time"));
            if ($dt instanceof \DateTime) {
                return $dt->getTimestamp();
            }
        }

        $ts = strtotime(trim("$date $time"));
        return $ts !== false ? $ts : (int) $fallbackTs;
    }
}
