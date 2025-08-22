<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\DatabaseException;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    protected $database;

  public function __construct()
{
    try {
        $serviceAccount = [
            'type'         => 'service_account',
            'project_id'   => (string) config('services.firebase.project_id'),
            'client_email' => (string) config('services.firebase.client_email'),
            'private_key'  => str_replace('\n', "\n", (string) config('services.firebase.private_key')),
        ];

        $firebase = (new Factory())
            ->withServiceAccount($serviceAccount)
            ->withDatabaseUri((string) config('services.firebase.database_url'));

        $this->database = $firebase->createDatabase();
    } catch (\Throwable $e) {
        Log::critical('Firebase init failed', ['error' => $e->getMessage()]);
        abort(500, 'Service initialization error');
    }
}

    // ---------- Readers (scoped by station prefix) ----------
    public function getFireReports(string $prefix): array
    {
        try {
            $node = "{$prefix}FireStation/{$prefix}FireReport";
            $reports = $this->database->getReference($node)->getValue();
            $result = [];

            if ($reports) {
                foreach ($reports as $id => $report) {
                    $result[] = [
                        'id' => $id,
                        'name' => $report['name'] ?? null,
                        'contact' => $report['contact'] ?? null,
                        'fireStartTime' => $report['fireStartTime'] ?? null,
                        'numberOfHousesAffected' => $report['numberOfHousesAffected'] ?? null,
                        'alertLevel' => $report['alertLevel'] ?? null,
                        'date' => $report['date'] ?? null,
                        'reportTime' => $report['reportTime'] ?? null,
                        'latitude' => $report['latitude'] ?? null,
                        'longitude' => $report['longitude'] ?? null,
                        'location' => $report['location'] ?? null,
                        'exactLocation' => $report['exactLocation'] ?? null,
                        'status' => $report['status'] ?? 'Pending',
                        'timestamp' => $report['timestamp'] ?? null,
                        'read' => $report['read'] ?? null,
                    ];
                }
            }

            return $result;
        } catch (DatabaseException | FirebaseException $e) {
            Log::error("Error fetching fire reports from {$prefix}: " . $e->getMessage());
            return [];
        }
    }

    public function getOtherEmergencyReports(string $prefix): array
    {
        try {
            $node = "{$prefix}FireStation/{$prefix}OtherEmergency";
            $reports = $this->database->getReference($node)->getValue();
            $result = [];

            if ($reports) {
                foreach ($reports as $id => $report) {
                    $result[] = [
                        'id' => $id,
                        'emergencyType' => $report['emergencyType'] ?? null,
                        'name' => $report['name'] ?? null,
                        'contact' => $report['contact'] ?? null,
                        'date' => $report['date'] ?? null,
                        'reportTime' => $report['reportTime'] ?? null,
                        'latitude' => $report['latitude'] ?? null,
                        'longitude' => $report['longitude'] ?? null,
                        'location' => $report['location'] ?? null,
                        'exactLocation' => $report['exactLocation'] ?? '',
                        'lastReportedTime' => $report['lastReportedTime'] ?? null,
                        'timestamp' => $report['timestamp'] ?? null,
                        'status' => $report['status'] ?? 'Pending',
                        'read' => $report['read'] ?? null,
                    ];
                }
            }

            return $result;
        } catch (DatabaseException | FirebaseException $e) {
            Log::error("Error fetching other emergency reports from {$prefix}: " . $e->getMessage());
            return [];
        }
    }

    // ---------- Status updates ----------
    public function updateReportStatus(string $prefix, string $incidentId, string $status, bool $isOtherEmergency = false): bool
    {
        try {
            $base = $isOtherEmergency
                ? "{$prefix}FireStation/{$prefix}OtherEmergency"
                : "{$prefix}FireStation/{$prefix}FireReport";

            $this->database->getReference("{$base}/{$incidentId}")->update(['status' => $status]);
            return true;
        } catch (\Exception $e) {
            Log::error("Error updating report status {$prefix}/{$incidentId}: " . $e->getMessage());
            return false;
        }
    }

    public function updateSmsReportStatus(string $prefix, string $incidentId, string $status): bool
{
    try {
        // Try both possible nodes the Android app uses
        $candidates = [
            "{$prefix}FireStation/{$prefix}SmsReport",
            "{$prefix}FireStation/SmsReport",
            "SmsReport", // legacy/root fallback
        ];

        $found = false;
        foreach ($candidates as $node) {
            $ref = $this->database->getReference("$node/$incidentId");
            if ($ref->getSnapshot()->exists()) {
                $ref->update(['status' => $status]);
                $found = true;
                break;
            }
        }

        // If not found, write to the first candidate to avoid silent failure
        if (!$found) {
            $this->database
                ->getReference($candidates[0]."/$incidentId")
                ->update(['status' => $status]);
        }

        return true;
    } catch (\Throwable $e) {
        Log::error("updateSmsReportStatus error: ".$e->getMessage());
        return false;
    }
}




    public function updateScopedReportStatus(string $prefix, string $reportType, string $incidentId, string $status): bool
    {
        try {
            $base = $reportType === 'otherEmergency'
                ? "{$prefix}FireStation/{$prefix}OtherEmergency"
                : "{$prefix}FireStation/{$prefix}FireReport";

            $this->database->getReference("{$base}/{$incidentId}")->update(['status' => $status]);
            return true;
        } catch (\Throwable $e) {
            Log::error("updateScopedReportStatus error: " . $e->getMessage());
            return false;
        }
    }

    // ---------- Unified chat (messages under incident) ----------
    /**
     * {Prefix}FireStation/{Prefix}{FireReport|OtherEmergency}/{incidentId}/messages/{pushKey}
     */
    public function storeUnifiedMessage(
        string $prefix,
        string $reportType,   // 'fireReports' | 'otherEmergency'
        string $incidentId,
        array $message
    ): bool {
        try {
            $base = $reportType === 'otherEmergency'
                ? "{$prefix}FireStation/{$prefix}OtherEmergency"
                : "{$prefix}FireStation/{$prefix}FireReport";

            $path = "{$base}/{$incidentId}/messages";
            $this->database->getReference($path)->push()->set($message);
            return true;
        } catch (\Throwable $e) {
            Log::error("storeUnifiedMessage error: " . $e->getMessage());
            return false;
        }
    }

    // ---------- Station inbox summary ----------
    /**
     * {Prefix}FireStation/ResponseMessage/{pushKey}
     */
    public function storeStationResponseSummary(string $prefix, array $payload): bool
    {
        try {
            $path = "{$prefix}FireStation/ResponseMessage";
            $this->database->getReference($path)->push()->set($payload);
            return true;
        } catch (\Throwable $e) {
            Log::error("storeStationResponseSummary error: " . $e->getMessage());
            return false;
        }
    }

  private function normalizeTime(?string $t): ?string
{
    if (!$t) return null;

    // Case 1: Already looks like HH:MM:SS
    if (preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $t)) {
        return $t; // keep as is (24h)
    }

    // Case 2: Parse AM/PM style like "11:23 PM"
    $parsed = strtotime($t);
    if ($parsed) {
        return date('H:i:s', $parsed);
    }

    return $t; // fallback
}

public function getSmsReports(string $prefix): array
{
    try {
        $candidates = [
            "{$prefix}FireStation/SmsReport",
            "{$prefix}FireStation/{$prefix}SmsReport",
        ];

        $raw = null;
        foreach ($candidates as $node) {
            $tmp = $this->database->getReference($node)->getValue();
            if ($tmp) { $raw = $tmp; break; }
        }
        if (!$raw) return [];

        $out = [];
        foreach ($raw as $id => $report) {
            $out[] = [
                'id'        => $id,
                'name'      => $report['name'] ?? null,
                'location'  => $report['location'] ?? null,
                'fireReport'=> $report['fireReport'] ?? $report['message'] ?? null,
                'date'      => $report['date'] ?? null,
                'time'      => $this->normalizeTime($report['time'] ?? null),
                'contact'   => $report['contact'] ?? null,
                'latitude'  => $report['latitude'] ?? null,
                'longitude' => $report['longitude'] ?? null,
                'status'    => ucfirst(strtolower($report['status'] ?? 'Pending')),
                'timestamp' => $report['timestamp'] ?? $report['createdAt'] ?? $report['updatedAt'] ?? null,
                'fireStationName' => $report['fireStationName'] ?? null,
            ];
        }
        return $out;
    } catch (\Throwable $e) {
        Log::error("Error fetching SMS reports ($prefix): ".$e->getMessage());
        return [];
    }
}




}
