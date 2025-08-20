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

    // ---------- SMS (unchanged) ----------
  public function getSmsReports(string $prefix): array
{
    try {
        // Correct the path to point directly to the SmsReport node
        $reference = $this->database->getReference("{$prefix}FireStation/SmsReport");
        $reports = $reference->getValue();
        $result = [];

        if ($reports) {
            foreach ($reports as $id => $report) {
                $result[] = [
                    'id' => $id,
                    'name' => $report['name'] ?? null,
                    'location' => $report['location'] ?? null,
                    'fireReport' => $report['fireReport'] ?? null,
                    'date' => $report['date'] ?? null,
                    'time' => $report['time'] ?? null,
                    'latitude' => $report['latitude'] ?? null,
                    'longitude' => $report['longitude'] ?? null,
                    'status' => $report['status'] ?? 'Pending',
                ];
            }
        }

        return $result;
    } catch (\Exception $e) {
        Log::error("Error fetching SMS reports: " . $e->getMessage());
        return [];
    }
}


}
