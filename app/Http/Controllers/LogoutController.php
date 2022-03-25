<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

/**
 * @deprecated
 * Use Auth/AuthenticatedSessionController instead
 */
class LogoutController extends Controller
{
    public function index()
    {
        Auth::logout();

        return redirect()->route('login');
    }
}
