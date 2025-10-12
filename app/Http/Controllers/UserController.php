<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function manageUsers()
    {
        // Fetch users from Firebase Realtime Database
        $users = $this->firebaseService->getUsers();

        // Return the view with the users data
        return view('admin.manage-users', compact('users'));
    }
}
