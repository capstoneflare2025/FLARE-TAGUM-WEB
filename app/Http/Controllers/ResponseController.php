<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ResponseController extends Controller
{
    protected FirebaseService $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Map any incoming reportType variant to the service key used by FirebaseService.
     * FirebaseService expects: 'fire' | 'otherEmergency' | 'emergencyMedicalServices' | 'sms'
     */
    private function normalizeReportType(string $t): string
    {
        $t = strtolower(trim($t));

        if (in_array($t, ['fire', 'firereports', 'tagumfire'], true)) {
            return 'fire';
        }
        if (in_array($t, ['otheremergency', 'othereports', 'tagumotheremergency'], true)) {
            return 'otherEmergency';
        }
        if (in_array($t, ['ems', 'emsreports', 'emergencymedicalservices', 'tagumems'], true)) {
            return 'emergencyMedicalServices';
        }
        if (in_array($t, ['sms', 'smsreport', 'smsreports', 'tagumsms'], true)) {
            return 'sms';
        }

        return 'otherEmergency';
    }

    /**
     * PATCH/POST: Update a report's status.
     * Body: { prefix, reportType, incidentId, status }
     */
    public function updateReportStatus(Request $request)
    {
        try {
            $data = $request->validate([
                'prefix'     => 'required|string',
                'incidentId' => 'required|string',
                'reportType' => 'required|string|in:fire,fireReports,otherEmergency,emsReports,emergencyMedicalServices,sms,smsReports',
                'status'     => 'required|string',
            ]);

            $normalizedType = $this->normalizeReportType($data['reportType']);

            // prefix ignored in new structure, but method signature preserved
            $ok = $this->firebaseService->updateScopedReportStatus(
                $data['prefix'],
                $normalizedType,
                $data['incidentId'],
                $data['status']
            );

            return response()->json(['success' => (bool) $ok]);
        } catch (\Throwable $e) {
            Log::error("Error updating report status: {$e->getMessage()}", ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Failed to update report status'], 500);
        }
    }

    /**
     * POST: Store a station reply into the incident thread + ResponseMessage list.
     * Body: { prefix, reportType, incidentId, responseMessage, reporterName?, contact?, fireStationName? }
     */
    public function storeResponse(Request $request)
    {
        try {
            $data = $request->validate([
                'prefix'          => 'required|string',
                'reportType'      => 'required|string|in:fire,fireReports,otherEmergency,emsReports,emergencyMedicalServices,sms,smsReports',
                'incidentId'      => 'required|string',
                'responseMessage' => 'required|string',
                'reporterName'    => 'nullable|string',
                'contact'         => 'nullable|string',
                'fireStationName' => 'nullable|string',
            ]);

            $normalizedType = $this->normalizeReportType($data['reportType']);

            $now   = now();
            $nowMs = (int) round(microtime(true) * 1000);

            $fsNice  = $data['fireStationName'] ?? 'Tagum City Central Fire Station';
            $contact = $data['contact'] ?? '';
            $name    = $data['reporterName'] ?? '';

            // 1) Thread message (stored under incident)
            $threadMsg = [
                'type'         => 'response',
                'text'         => $data['responseMessage'],
                'imageBase64'  => null,
                'audioBase64'  => null,
                'uid'          => null,
                'reporterName' => $name,
                'contact'      => $contact,
                'date'         => $now->format('Y-m-d'),
                'time'         => $now->format('H:i:s'),
                'timestamp'    => $nowMs,
                'isRead'       => false,
            ];

            $ok1 = $this->firebaseService->storeUnifiedMessage(
                $data['prefix'],           // retained for compatibility
                $normalizedType,
                $data['incidentId'],
                $threadMsg
            );

            // 2) Station-wide ResponseMessage entry (AllReport/ResponseMessage)
            $summary = [
                'uid'             => null,
                'fireStationName' => $fsNice,
                'incidentId'      => $data['incidentId'],
                'reporterName'    => $name,
                'contact'         => $contact,
                'responseMessage' => $data['responseMessage'],
                'responseDate'    => $now->format('Y-m-d'),
                'responseTime'    => $now->format('H:i:s'),
                'imageBase64'     => null,
                'timestamp'       => $nowMs,
                'isRead'          => false,
            ];

            $ok2 = $this->firebaseService->storeStationResponseSummary($data['prefix'], $summary);

            return response()->json(['success' => ($ok1 && $ok2)]);
        } catch (\Throwable $e) {
            Log::error("Error storing response: {$e->getMessage()}", ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Failed to store response'], 500);
        }
    }
}
