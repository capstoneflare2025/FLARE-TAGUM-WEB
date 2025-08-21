<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth as FirebaseAuth;
use Google\Cloud\Core\Exception\GoogleException;

class AuthController extends Controller
{
    protected FirebaseAuth $auth;

    public function __construct()
    {
        try {
            $serviceAccount = [
                'type'         => 'service_account',
                'project_id'   => (string) config('services.firebase.project_id'),
                'client_email' => (string) config('services.firebase.client_email'),
                'private_key'  => str_replace('\n', "\n", (string) config('services.firebase.private_key')),
            ];

            $this->auth = (new Factory())
                ->withServiceAccount($serviceAccount)
                ->createAuth();
        } catch (\Throwable $e) {
            Log::critical('Firebase Auth init failed', ['error' => $e->getMessage()]);
            abort(500, 'Service initialization error');
        }
    }

   public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    $email = $request->input('email');
    $password = $request->input('password');

    // Define the list of allowed emails
    $allowedEmails = [
        'mabini123@gmail.com',
        'lafilipina123@gmail.com',
        'canocotan123@gmail.com'
    ];

    // Check if the email is in the allowed list
    if (!in_array($email, $allowedEmails)) {
        return back()->withErrors(['login' => 'Invalid credentials.'])->withInput();
    }

    try {
        $firebaseApiKey = config('services.firebase.api_key');

        // Send request to Firebase Authentication
        $response = Http::post("https://identitytoolkit.googleapis.com/v1/accounts:signInWithPassword?key={$firebaseApiKey}", [
            'email' => $email,
            'password' => $password,
            'returnSecureToken' => true
        ]);

        if (!$response->ok()) {
            // Do not return specific password errors
            return back()->withErrors(['login' => 'Invalid credentials.'])->withInput();
        }

        $idToken = $response->json()['idToken'];

        // Verify ID Token with Firebase Auth
        $verifiedIdToken = $this->auth->verifyIdToken($idToken);
        $uid = $verifiedIdToken->claims()->get('sub'); // Extract the Firebase UID

        // Log the Firebase data (for debugging)
        Log::info('Firebase Authentication Successful', ['uid' => $uid, 'email' => $email]);

        // Store Firebase data in session
        Session::put('firebase_user_email', $email); // Storing email in session
        Session::put('firebase_user_uid', $uid); // Storing email in session

        // Log session data to ensure it's set correctly
        Log::info('Session data after login', ['session' => session()->all()]);

        // Redirect to dashboard
        return redirect()->route('incident-reports');

    } catch (\Throwable $e) {
        Log::error('Login failed', ['error' => $e->getMessage()]);
        return back()->withErrors(['login' => 'Login failed: ' . $e->getMessage()])->withInput();
    }
}




    public function logout()
    {
        Session::forget('firebase_user_email');
        return redirect()->route('login');
    }
}
