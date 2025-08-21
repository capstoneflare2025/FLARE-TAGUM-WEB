<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\Request;

class IncidentReportsController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    // App/Http/Controllers/IncidentReportsController.php

// App/Http/Controllers/IncidentReportsController.php

public function index(Request $request)
{
    if (!session()->has('firebase_user_email')) {
        return redirect()->route('login')->with('error', 'You must be logged in to view this page.');
    }

    $email = strtolower(session('firebase_user_email'));
    $prefix = match ($email) {
        'canocotan123@gmail.com'  => 'Canocotan',
        'lafilipina123@gmail.com' => 'LaFilipina',
        'mabini123@gmail.com'     => 'Mabini',
        default                   => null
    };

    if (!$prefix) {
        return view('ADMIN-DASHBOARD.incident-reports', ['fireReports' => [], 'otherEmergencyReports' => []]);
    }

    try {
        $fireReports = $this->firebase->getFireReports($prefix);
        $otherEmergencyReports = $this->firebase->getOtherEmergencyReports($prefix);

        // Do not filter out items; sort with a resilient key
        $fireReports = collect($fireReports)
            ->sortByDesc(fn ($r) => $this->sortKey($r))
            ->values()
            ->all();

        $otherEmergencyReports = collect($otherEmergencyReports)
            ->sortByDesc(fn ($r) => $this->sortKey($r))
            ->values()
            ->all();

    } catch (\Throwable $e) {
        return redirect()->back()->with('error', 'Unable to fetch incident reports. Please try again.');
    }

    return view('ADMIN-DASHBOARD.incident-reports', compact('fireReports', 'otherEmergencyReports'));
}

private function sortKey(array $r): int
{
    if (!empty($r['timestamp']) && is_numeric($r['timestamp'])) {
        return (int) $r['timestamp'];
    }

    $d = $r['date'] ?? '';
    $t = $r['reportTime'] ?? ($r['time'] ?? '00:00');
    $ts = $this->parseDmyTime($d, $t);
    return $ts > 0 ? $ts : 0;
}

private function parseDmyTime(string $d, string $t): int
{
    if ($d === '') return 0;

    $dNorm = str_replace('-', '/', $d);
    $p = explode('/', $dNorm);

    if (count($p) === 3) {
        [$day, $month, $year] = $p;
        if (strlen($year) === 2) $year = '20'.$year;
        $iso = sprintf('%04d-%02d-%02d %s', (int)$year, (int)$month, (int)$day, $t ?: '00:00');
        return strtotime($iso) ?: 0;
    }

    return strtotime(trim($d.' '.$t)) ?: 0;
}


}
