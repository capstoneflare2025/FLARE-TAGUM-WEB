<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Database;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\DatabaseException;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    protected Database $database;

    /** New fixed station root */
    private string $stationRoot = 'TagumCityCentralFireStation/AllReport';

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

    /* ---------------------------------------------------------
     * Helpers
     * --------------------------------------------------------- */

    /**
     * Map a logical report type to its Firebase node path.
     *
     * Accepted $reportType:
     *   'fire' | 'otherEmergency' | 'emergencyMedicalServices' | 'sms'
     *
     * NEW Result pattern:
     *   TagumCityCentralFireStation/AllReport/{FireReport|OtherEmergencyReport|EmergencyMedicalServicesReport|SmsReport}
     *
     * NOTE: $prefix is kept for compatibility but not used anymore.
     */
    private function baseNode(string $prefix, string $reportType): string
    {
        $root = $this->stationRoot;

        switch ($reportType) {
            case 'fire':
                return "{$root}/FireReport";
            case 'otherEmergency':
                return "{$root}/OtherEmergencyReport";
            case 'emergencyMedicalServices':
                return "{$root}/EmergencyMedicalServicesReport";
            case 'sms':
                return "{$root}/SmsReport";
            default:
                return "{$root}/OtherEmergencyReport";
        }
    }

    /** Normalize HH:mm[:ss] to 24h; allow AM/PM inputs too. */
    private function normalizeTime(?string $t): ?string
    {
        if ($t === null || $t === '') return $t;
        if (preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $t)) {
            return strlen($t) === 5 ? $t.':00' : $t;
        }
        $parsed = strtotime($t);
        return $parsed ? date('H:i:s', $parsed) : $t;
    }

    /** Prefer Android's timeStamp; fall back gracefully. */
    private function pickTimestamp(array $report)
    {
        return $report['timeStamp']   // Android FireReport
            ?? $report['timestamp']   // Other/EMS/SMS + legacy
            ?? $report['createdAt']
            ?? $report['updatedAt']
            ?? null;
    }

    /* ---------------------------------------------------------
     * Readers
     * --------------------------------------------------------- */

    /** Fire: TagumCityCentralFireStation/AllReport/FireReport */
    public function getFireReports(string $prefix): array
    {
        try {
            $node = $this->baseNode($prefix, 'fire');
            $reports = $this->database->getReference($node)->getValue();
            $out = [];

            if ($reports) {
                foreach ($reports as $id => $r) {
                    $out[] = [
                        'id'              => $id,
                        'name'            => $r['name'] ?? null,
                        'contact'         => $r['contact'] ?? null,
                        'type'            => $r['type'] ?? null,
                        'date'            => $r['date'] ?? null,              // "MM/dd/yyyy"
                        'reportTime'      => $this->normalizeTime($r['reportTime'] ?? null),
                        'latitude'        => $r['latitude'] ?? null,
                        'longitude'       => $r['longitude'] ?? null,
                        'exactLocation'   => $r['exactLocation'] ?? null,
                        'mapLink'         => $r['mapLink'] ?? null,
                        'location'        => $r['mapLink'] ?? ($r['location'] ?? null),
                        'status'          => $r['status'] ?? 'Pending',
                        'read'            => $r['read'] ?? false,
                        'fireStationName' => $r['fireStationName'] ?? null,
                        'photoBase64'     => $r['photoBase64'] ?? null,
                        'timestamp'       => $this->pickTimestamp($r),
                    ];
                }
            }
            return $out;
        } catch (DatabaseException | FirebaseException $e) {
            Log::error("Error fetching fire reports: ".$e->getMessage());
            return [];
        }
    }

    /** Other Emergency: TagumCityCentralFireStation/AllReport/OtherEmergencyReport */
    public function getOtherEmergencyReports(string $prefix): array
    {
        try {
            $node = $this->baseNode($prefix, 'otherEmergency');
            $reports = $this->database->getReference($node)->getValue();
            $out = [];

            if ($reports) {
                foreach ($reports as $id => $r) {
                    $out[] = [
                        'id'              => $id,
                        'emergencyType'   => $r['emergencyType'] ?? null,
                        'name'            => $r['name'] ?? null,
                        'contact'         => $r['contact'] ?? null,
                        'date'            => $r['date'] ?? null,              // "MM/dd/yy"
                        'reportTime'      => $this->normalizeTime($r['reportTime'] ?? null),
                        'latitude'        => $r['latitude'] ?? null,
                        'longitude'       => $r['longitude'] ?? null,
                        'location'        => $r['location'] ?? null,          // Google Maps URL
                        'exactLocation'   => $r['exactLocation'] ?? '',
                        'lastReportedTime'=> $r['lastReportedTime'] ?? null,
                        'timestamp'       => $this->pickTimestamp($r),
                        'status'          => $r['status'] ?? 'Pending',
                        'read'            => $r['read'] ?? false,
                        'fireStationName' => $r['fireStationName'] ?? null,
                        'photoBase64'     => $r['photoBase64'] ?? null,
                    ];
                }
            }
            return $out;
        } catch (DatabaseException | FirebaseException $e) {
            Log::error("Error fetching other emergency reports: ".$e->getMessage());
            return [];
        }
    }

    /** EMS: TagumCityCentralFireStation/AllReport/EmergencyMedicalServicesReport */
    public function getEmergencyMedicalServicesReports(string $prefix): array
    {
        try {
            $node = $this->baseNode($prefix, 'emergencyMedicalServices');
            $reports = $this->database->getReference($node)->getValue();
            $out = [];

            if ($reports) {
                foreach ($reports as $id => $r) {
                    $out[] = [
                        'id'              => $id,
                        'type'            => $r['type'] ?? null,
                        'name'            => $r['name'] ?? null,
                        'contact'         => $r['contact'] ?? null,
                        'date'            => $r['date'] ?? null,              // "MM/dd/yy"
                        'reportTime'      => $this->normalizeTime($r['reportTime'] ?? null),
                        'latitude'        => $r['latitude'] ?? null,
                        'longitude'       => $r['longitude'] ?? null,
                        'location'        => $r['location'] ?? null,
                        'exactLocation'   => $r['exactLocation'] ?? null,
                        'status'          => $r['status'] ?? 'Pending',
                        'timestamp'       => $this->pickTimestamp($r),
                        'read'            => $r['read'] ?? false,
                        'fireStationName' => $r['fireStationName'] ?? null,
                        'photoBase64'     => $r['photoBase64'] ?? null,
                    ];
                }
            }
            return $out;
        } catch (DatabaseException | FirebaseException $e) {
            Log::error("Error fetching EMS reports: ".$e->getMessage());
            return [];
        }
    }

        // Inside FirebaseService

        public function getUsers()
        {
            try {
                $users = [];
                $userRef = $this->database->getReference('Users');  // Refers to the "Users" node in Firebase Realtime Database

                // Fetch all users from Firebase Realtime Database
                $snapshot = $userRef->getValue();

                // If there are users, process them
                if ($snapshot) {
                    foreach ($snapshot as $key => $userData) {
                        $users[] = [
                            'name' => $userData['name'] ?? 'No Name',  // User's name
                            'contact' => $userData['contact'] ?? 'No Contact',  // User's contact number
                            'email' => $userData['email'] ?? 'No Email',  // User's email address
                        ];
                    }
                }

                return $users;
            } catch (\Throwable $e) {
                Log::error("Error fetching users: " . $e->getMessage());
                return [];
            }
        }



    /* ---------------------------------------------------------
     * Status updates
     * --------------------------------------------------------- */

    /**
     * Kept signature; path now points to station root AllReport tree.
     */
    public function updateReportStatus(string $prefix, string $incidentId, string $status, bool $isOtherEmergency = false): bool
    {
        try {
            $base = $isOtherEmergency
                ? $this->baseNode($prefix, 'otherEmergency')
                : $this->baseNode($prefix, 'fire');

            $this->database->getReference("{$base}/{$incidentId}")->update(['status' => $status]);
            return true;
        } catch (\Throwable $e) {
            Log::error("Error updating report status {$incidentId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Explicit scoped updater (signature kept).
     * $reportType: 'fire' | 'otherEmergency' | 'emergencyMedicalServices' | 'sms'
     */
    public function updateScopedReportStatus(string $prefix, string $reportType, string $incidentId, string $status): bool
    {
        try {
            $base = $this->baseNode($prefix, $reportType);
            $this->database->getReference("{$base}/{$incidentId}")->update(['status' => $status]);
            return true;
        } catch (\Throwable $e) {
            Log::error("updateScopedReportStatus error: " . $e->getMessage());
            return false;
        }
    }

    /* ---------------------------------------------------------
     * Unified chat (messages under incident)
     * --------------------------------------------------------- */

    /**
     * New path:
     *   TagumCityCentralFireStation/AllReport/{FireReport|OtherEmergencyReport|EmergencyMedicalServicesReport|SmsReport}/{incidentId}/messages/{pushKey}
     */
    public function storeUnifiedMessage(
        string $prefix,
        string $reportType,
        string $incidentId,
        array $message
    ): bool {
        try {
            $base = $this->baseNode($prefix, $reportType);
            $path = "{$base}/{$incidentId}/messages";
            $this->database->getReference($path)->push()->set($message);
            return true;
        } catch (\Throwable $e) {
            Log::error("storeUnifiedMessage error: " . $e->getMessage());
            return false;
        }
    }

    /* ---------------------------------------------------------
     * Station inbox summary
     * --------------------------------------------------------- */

    /**
     * New path:
     *   TagumCityCentralFireStation/AllReport/ResponseMessage/{pushKey}
     */
    public function storeStationResponseSummary(string $prefix, array $payload): bool
    {
        try {
            $path = $this->stationRoot . '/ResponseMessage';
            $this->database->getReference($path)->push()->set($payload);
            return true;
        } catch (\Throwable $e) {
            Log::error("storeStationResponseSummary error: " . $e->getMessage());
            return false;
        }
    }

    /* ---------------------------------------------------------
     * SMS reports
     * --------------------------------------------------------- */

    /**
     * Now reads ONLY from:
     *   TagumCityCentralFireStation/AllReport/SmsReport
     */
    public function getSmsReports(string $prefix): array
    {
        try {
            $node = $this->baseNode($prefix, 'sms');
            $raw  = $this->database->getReference($node)->getValue();
            if (!$raw) return [];

            $out = [];
            foreach ($raw as $id => $report) {
                $out[] = [
                    'id'             => $id,
                    'name'           => $report['name'] ?? null,
                    'location'       => $report['location'] ?? null,
                    'fireReport'     => $report['fireReport'] ?? $report['message'] ?? null,
                    'date'           => $report['date'] ?? null,
                    'time'           => $this->normalizeTime($report['time'] ?? null),
                    'contact'        => $report['contact'] ?? null,
                    'latitude'       => $report['latitude'] ?? null,
                    'longitude'      => $report['longitude'] ?? null,
                    'status'         => ucfirst(strtolower($report['status'] ?? 'Pending')),
                    'timestamp'      => $this->pickTimestamp($report),
                    'fireStationName'=> $report['fireStationName'] ?? null,
                ];
            }
            return $out;
        } catch (\Throwable $e) {
            Log::error("Error fetching SMS reports: ".$e->getMessage());
            return [];
        }
    }

    /**
     * Now updates ONLY at:
     *   TagumCityCentralFireStation/AllReport/SmsReport/{incidentId}
     */
    public function updateSmsReportStatus(string $prefix, string $incidentId, string $status): bool
    {
        try {
            $node = $this->baseNode($prefix, 'sms') . "/{$incidentId}";
            $this->database->getReference($node)->update(['status' => $status]);
            return true;
        } catch (\Throwable $e) {
            Log::error("updateSmsReportStatus error: ".$e->getMessage());
            return false;
        }
    }
}
