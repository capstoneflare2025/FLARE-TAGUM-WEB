<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth as FirebaseAuth;
use Kreait\Firebase\Database;

class AuthController extends Controller
{
    protected FirebaseAuth $auth;
    protected Database $db;

    public function __construct()
    {
        try {
            $serviceAccount = [
                'type'         => 'service_account',
                'project_id'   => (string) config('services.firebase.project_id'),
                'client_email' => (string) config('services.firebase.client_email'),
                'private_key'  => str_replace('\n', "\n", (string) config('services.firebase.private_key')),
            ];

            $factory = (new Factory())->withServiceAccount($serviceAccount);

            // Optional if you have a Realtime DB URL in config:
            if (config('services.firebase.database_url')) {
                $factory = $factory->withDatabaseUri(config('services.firebase.database_url'));
            }

            $this->auth = $factory->createAuth();
            $this->db   = $factory->createDatabase();

        } catch (\Throwable $e) {
            Log::critical('Firebase init failed', ['error' => $e->getMessage()]);
            abort(500, 'Service initialization error');
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        $email = strtolower($request->input('email'));
        $password = (string) $request->input('password');

        try {
            // 1) Sign in with Firebase Auth (password is only in Auth, not in Profile)
            $firebaseApiKey = (string) config('services.firebase.api_key');

            $resp = Http::post("https://identitytoolkit.googleapis.com/v1/accounts:signInWithPassword?key={$firebaseApiKey}", [
                'email' => $email,
                'password' => $password,
                'returnSecureToken' => true,
            ]);

            if (!$resp->ok()) {
                return back()->withErrors(['login' => 'Invalid credentials.'])->withInput();
            }

            $idToken = $resp->json('idToken');
            $verifiedIdToken = $this->auth->verifyIdToken($idToken);
            $uid = (string) $verifiedIdToken->claims()->get('sub');

            // 2) Authorization: check that this account is allowed for the station
            $stationPath = 'TagumCityCentralFireStation/Profile';
            $profile = $this->db->getReference($stationPath)->getValue() ?? [];

            // Build a set of allowed emails (handles either "email" or "emails")
            $allowedEmails = [];

            if (isset($profile['email']) && is_string($profile['email'])) {
                $allowedEmails[] = strtolower($profile['email']);
            }

            // If "emails" is an array or map
            if (isset($profile['emails'])) {
                if (is_array($profile['emails'])) {
                    foreach ($profile['emails'] as $k => $v) {
                        // support ["a@x.com","b@y.com"] or {"uid1":"a@x.com","uid2":"b@y.com"}
                        if (is_string($v)) {
                            $allowedEmails[] = strtolower($v);
                        } elseif (is_array($v) && isset($v['email']) && is_string($v['email'])) {
                            $allowedEmails[] = strtolower($v['email']);
                        }
                    }
                } elseif (is_string($profile['emails'])) {
                    // edge case: single string
                    $allowedEmails[] = strtolower($profile['emails']);
                }
            }

            // Optional: also allow by UID if your Profile stores a users map
            $allowedUids = [];
            if (isset($profile['users']) && is_array($profile['users'])) {
                // supports {"uid123": true, "uid456": true} OR {"uid123": {"active":true}}
                foreach ($profile['users'] as $key => $val) {
                    if (is_string($key)) {
                        $allowedUids[] = $key;
                    }
                }
            }

            $emailAuthorized = in_array($email, array_values(array_unique($allowedEmails)), true);
            $uidAuthorized   = in_array($uid, $allowedUids, true);

            if (!$emailAuthorized && !$uidAuthorized) {
                Log::warning('Auth rejected: not authorized for station', [
                    'uid' => $uid, 'email' => $email, 'station' => 'TagumCityCentralFireStation'
                ]);
                return back()->withErrors(['login' => 'You are not authorized for this station.'])->withInput();
            }

            // 3) Authorized → persist session
            Session::put('firebase_user_email', $email);
            Session::put('firebase_user_uid', $uid);
            Session::put('station', 'TagumCityCentralFireStation');

            Log::info('Login success', ['uid' => $uid, 'email' => $email, 'station' => 'TagumCityCentralFireStation']);

            return redirect()->route('incident-reports');

        } catch (\Throwable $e) {
            Log::error('Login failed', ['error' => $e->getMessage()]);
            return back()->withErrors(['login' => 'Login failed: ' . $e->getMessage()])->withInput();
        }
    }

    public function logout()
    {
        Session::forget('firebase_user_email');
        Session::forget('firebase_user_uid');
        Session::forget('station');
        return redirect()->route('login');
    }
}
